function doGet(e) {
  if (e && e.parameter && e.parameter.evidenciaId) {
    const evidenciaId = String(e.parameter.evidenciaId || "").trim();
    if (!/^[A-Za-z0-9_-]{10,}$/.test(evidenciaId)) {
      return HtmlService.createHtmlOutput("Evidencia no valida.");
    }

    try {
      const file = DriveApp.getFileById(evidenciaId);
      if (!esEvidenciaHtmlValida_(file)) {
        return HtmlService.createHtmlOutput("Acceso denegado.");
      }
      const html = file.getBlob().getDataAsString();
      return HtmlService.createHtmlOutput(html)
        .setTitle(file.getName())
        .addMetaTag("viewport", "width=device-width, initial-scale=1")
        .setXFrameOptionsMode(HtmlService.XFrameOptionsMode.ALLOWALL);
    } catch (error) {
      return HtmlService.createHtmlOutput("Evidencia no encontrada.");
    }
  }

  return HtmlService.createHtmlOutputFromFile("Index")
    .setTitle("Formato de Atención a Usuarios - Proceso Informático")
    .addMetaTag("viewport", "width=device-width, initial-scale=1");
}

const REGLAS_FORMULARIO_ = {
  required: [
    "fechaAtencion",
    "servidorPublico",
    "servidorCedula",
    "areaEntidad",
    "tecnicoNombre",
    "tecnicoCedula",
    "descripcion",
  ],
  maxLengths: {
    idSolicitud: 20,
    fechaAtencion: 10,
    equipo: 60,
    servidorPublico: 80,
    servidorCedula: 15,
    servidorCargo: 80,
    areaEntidad: 80,
    tecnicoNombre: 80,
    tecnicoCedula: 15,
    descripcion: 500,
    diagnostico: 500,
    soporteExterno: 3,
    soporteExternoObservaciones: 500,
    resolvioNecesidad: 3,
    estadoCaso: 12,
    fechaCierre: 10,
    expectativas: 300,
    clasificacionTexto: 600,
    clasificacionCeldas: 200,
  },
  maxFotos: 5,
  maxFotoBytes: 3 * 1024 * 1024,
  allowedMimeTypes: ["image/jpeg", "image/png", "image/webp"],
};

const CAMPOS_LABEL_ = {
  fechaAtencion: "Fecha Atencion",
  servidorPublico: "Profesional / Usuario",
  servidorCedula: "Cedula del Profesional / Usuario",
  areaEntidad: "Area o Entidad",
  tecnicoNombre: "Nombre del Tecnico",
  tecnicoCedula: "Cedula del Tecnico",
  descripcion: "Descripcion del Requerimiento",
};

function obtenerSiguienteId() {
  const lock = LockService.getScriptLock();
  lock.waitLock(10000);
  try {
    const props = PropertiesService.getScriptProperties();
    let ultimoNumero = parseInt(props.getProperty("ultimoIdSolicitud"), 10);
    if (!ultimoNumero) {
      ultimoNumero = obtenerUltimoIdDesdeHoja_();
    }

    const siguienteNumero = (ultimoNumero || 0) + 1;
    props.setProperty("ultimoIdSolicitud", String(siguienteNumero));
    return formatearIdSolicitud_(siguienteNumero);
  } finally {
    lock.releaseLock();
  }
}

function procesarFormularioWeb(formData) {
  if (!formData) {
    throw new Error("No se recibieron datos del formulario.");
  }

  const datos = normalizarFormulario_(formData);
  validarFormulario_(datos);

  const spreadsheet = SpreadsheetApp.getActiveSpreadsheet();
  const registroSheet = spreadsheet.getSheetByName("Registro de Solicitudes");

  if (!registroSheet) {
    throw new Error("No se encontro la hoja 'Registro de Solicitudes'.");
  }

  const solicitudId = String(datos.idSolicitud || "").trim();
  const idValido = /^CS-\d{4}$/.test(solicitudId);
  if (!idValido || idYaExiste_(registroSheet, solicitudId)) {
    datos.idSolicitud = obtenerSiguienteId();
  } else {
    datos.idSolicitud = solicitudId;
  }

  const evidencia = crearEvidenciaEnDrive_(datos);

  const filaRegistro = [
    datos.idSolicitud || "",
    datos.fechaAtencion || "",
    datos.equipo || "",
    datos.servidorPublico || "",
    datos.servidorCedula || "",
    datos.servidorCargo || "",
    datos.areaEntidad || "",
    datos.tecnicoNombre || "",
    datos.tecnicoCedula || "",
    datos.descripcion || "",
    datos.diagnostico || "",
    datos.soporteExterno || "",
    datos.soporteExternoObservaciones || "",
    datos.resolvioNecesidad || "",
    datos.estadoCaso || "",
    datos.fechaCierre || "",
    datos.expectativas || "",
    datos.clasificacionTexto || "",
    evidencia.url,
    evidencia.folderUrl,
    new Date(),
  ];

  registroSheet.appendRow(filaRegistro);

  return datos.idSolicitud || "";
}

function resetRegistroSolicitudes() {
  const sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName(
    "Registro de Solicitudes"
  );
  if (!sheet) {
    throw new Error("No se encontro la hoja 'Registro de Solicitudes'.");
  }

  sheet.clearContents();
  sheet.appendRow([
    "ID Solicitud",
    "Fecha Atencion",
    "Equipo",
    "Servidor Publico / Usuario",
    "Cedula Servidor Publico / Usuario",
    "Cargo Servidor Publico / Usuario",
    "Area o Entidad",
    "Nombre del Tecnico",
    "Cedula del Tecnico",
    "Descripcion del Requerimiento",
    "Diagnostico y Justificacion",
    "Soporte Externo",
    "Observaciones Soporte Externo",
    "Resolvio Necesidad",
    "Estado del caso",
    "Fecha de cierre",
    "Expectativas",
    "Clasificacion",
    "Evidencia HTML",
    "Carpeta evidencia",
    "Fecha de registro",
  ]);
}

function crearEvidenciaEnDrive_(formData) {
  const rootFolder = obtenerCarpetaEvidencias_();
  const solicitudFolder = obtenerCarpetaSolicitud_(rootFolder, formData);
  const htmlFolder = obtenerCarpetaHtml_();

  const fotos = guardarFotosEnCarpeta_(solicitudFolder, formData.fotosEvidencia);
  const html = crearHtmlEvidencia_(formData, fotos);
  const fileName = `Evidencia_${formData.idSolicitud}.html`;
  const file = htmlFolder.createFile(fileName, html, MimeType.HTML);
  const baseUrl = ScriptApp.getService().getUrl();
  const evidenceUrl = baseUrl
    ? `${baseUrl}?evidenciaId=${file.getId()}`
    : file.getUrl();

  return {
    url: evidenceUrl,
    folderUrl: solicitudFolder.getUrl(),
    fotosUrls: fotos.map((foto) => foto.url),
  };
}

function obtenerCarpetaEvidencias_() {
  const folderId = "104CZlqCCJuqH4CR5UcRCi5NL_-aqmO26";
  return DriveApp.getFolderById(folderId);
}

function obtenerCarpetaHtml_() {
  const folderId = "1rQvYtsapvKvl6dsMtHQLu4GoZhOr5mJS";
  return DriveApp.getFolderById(folderId);
}

function obtenerCarpetaSolicitud_(rootFolder, formData) {
  const equipo = formData.equipo || "Sin_equipo";
  const solicitudId = formData.idSolicitud || "Solicitud";
  const safeName = normalizarNombreCarpeta_(`${solicitudId} - ${equipo}`);
  const iterator = rootFolder.getFoldersByName(safeName);
  if (iterator.hasNext()) {
    return iterator.next();
  }
  return rootFolder.createFolder(safeName);
}

function normalizarNombreCarpeta_(value) {
  return String(value)
    .trim()
    .replace(/[\\/:*?"<>|]+/g, "-")
    .replace(/\s+/g, " ")
    .substring(0, 80)
    .trim();
}

function guardarFotosEnCarpeta_(folder, fotos) {
  if (!Array.isArray(fotos) || fotos.length === 0) {
    return [];
  }

  validarFotos_(fotos);

  return fotos
    .map((foto, index) => {
      if (!foto || !foto.data) {
        return null;
      }
      const safeName = normalizarNombreCarpeta_(foto.name || `foto_${index + 1}`);
      const fileName = `Foto_${String(index + 1).padStart(2, "0")}_${safeName}`;
      const blob = Utilities.newBlob(
        Utilities.base64Decode(foto.data),
        foto.mimeType || "image/jpeg",
        fileName
      );
      const file = folder.createFile(blob);
      const fileId = file.getId();
      return {
        name: fileName,
        url: file.getUrl(),
        viewUrl: `https://drive.google.com/uc?export=view&id=${fileId}`,
      };
    })
    .filter(Boolean);
}

function crearHtmlEvidencia_(formData, fotos) {
  const tz = Session.getScriptTimeZone();
  const fechaRegistro = Utilities.formatDate(new Date(), tz, "yyyy-MM-dd HH:mm");
  const filas = [
    ["ID Solicitud", formData.idSolicitud],
    ["Fecha Atencion", formData.fechaAtencion],
    ["Equipo", formData.equipo],
    ["Servidor Publico / Usuario", formData.servidorPublico],
    ["Cedula Servidor Publico / Usuario", formData.servidorCedula],
    ["Cargo Servidor Publico / Usuario", formData.servidorCargo],
    ["Area o Entidad", formData.areaEntidad],
    ["Nombre del Tecnico", formData.tecnicoNombre],
    ["Cedula del Tecnico", formData.tecnicoCedula],
    ["Descripcion del Requerimiento", formData.descripcion],
    ["Diagnostico y Justificacion", formData.diagnostico],
    ["Soporte Externo", formData.soporteExterno],
    ["Observaciones Soporte Externo", formData.soporteExternoObservaciones],
    ["Resolvio Necesidad", formData.resolvioNecesidad],
    ["Estado del caso", formData.estadoCaso],
    ["Fecha de cierre", formData.fechaCierre],
    ["Expectativas", formData.expectativas],
    ["Clasificacion", formData.clasificacionTexto],
    ["Fecha de registro", fechaRegistro],
  ];

  const rowsHtml = filas
    .map(
      ([label, value]) =>
        `<tr><th>${escapeHtml_(label)}</th><td>${escapeHtml_(formatearValor_(value))}</td></tr>`
    )
    .join("");

  return `<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Evidencia ${escapeHtml_(formatearValor_(formData.idSolicitud))}</title>
    <style>
      body { font-family: Arial, sans-serif; background: #f4f6fb; color: #1e2a44; padding: 24px; }
      .card { background: #ffffff; border-radius: 12px; padding: 24px; box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08); }
      h1 { font-size: 20px; margin: 0 0 16px; }
      table { width: 100%; border-collapse: collapse; }
      th, td { text-align: left; padding: 8px 10px; border-bottom: 1px solid #e6ecf5; vertical-align: top; }
      th { width: 280px; color: #5a6b88; font-weight: 600; }
    </style>
  </head>
  <body>
    <div class="card">
      <h1>Formato de Atencion a Usuarios - Proceso Informatico</h1>
      <table>${rowsHtml}</table>
    </div>
  </body>
</html>`;
}

function esEvidenciaHtmlValida_(file) {
  if (!file || file.getMimeType() !== MimeType.HTML) {
    return false;
  }
  if (!/^Evidencia_/.test(file.getName())) {
    return false;
  }
  const htmlFolderId = obtenerCarpetaHtml_().getId();
  const parents = file.getParents();
  while (parents.hasNext()) {
    if (parents.next().getId() === htmlFolderId) {
      return true;
    }
  }
  return false;
}

function normalizarFormulario_(formData) {
  const estadoCasoNormalizado = normalizarTexto_(
    formData.estadoCaso,
    REGLAS_FORMULARIO_.maxLengths.estadoCaso
  );
  const estadoCaso = estadoCasoNormalizado || "Abierto";
  let fechaCierre = normalizarTexto_(
    formData.fechaCierre,
    REGLAS_FORMULARIO_.maxLengths.fechaCierre
  );
  if (estadoCaso !== "Cerrado") {
    fechaCierre = "";
  }

  return {
    idSolicitud: normalizarTexto_(formData.idSolicitud, REGLAS_FORMULARIO_.maxLengths.idSolicitud),
    fechaAtencion: normalizarTexto_(
      formData.fechaAtencion,
      REGLAS_FORMULARIO_.maxLengths.fechaAtencion
    ),
    equipo: normalizarTexto_(formData.equipo, REGLAS_FORMULARIO_.maxLengths.equipo),
    servidorPublico: normalizarTexto_(
      formData.servidorPublico,
      REGLAS_FORMULARIO_.maxLengths.servidorPublico
    ),
    servidorCedula: normalizarTexto_(
      formData.servidorCedula,
      REGLAS_FORMULARIO_.maxLengths.servidorCedula
    ),
    servidorCargo: normalizarTexto_(
      formData.servidorCargo,
      REGLAS_FORMULARIO_.maxLengths.servidorCargo
    ),
    areaEntidad: normalizarTexto_(
      formData.areaEntidad,
      REGLAS_FORMULARIO_.maxLengths.areaEntidad
    ),
    tecnicoNombre: normalizarTexto_(
      formData.tecnicoNombre,
      REGLAS_FORMULARIO_.maxLengths.tecnicoNombre
    ),
    tecnicoCedula: normalizarTexto_(
      formData.tecnicoCedula,
      REGLAS_FORMULARIO_.maxLengths.tecnicoCedula
    ),
    descripcion: normalizarTexto_(
      formData.descripcion,
      REGLAS_FORMULARIO_.maxLengths.descripcion
    ),
    diagnostico: normalizarTexto_(
      formData.diagnostico,
      REGLAS_FORMULARIO_.maxLengths.diagnostico
    ),
    soporteExterno: normalizarTexto_(
      formData.soporteExterno,
      REGLAS_FORMULARIO_.maxLengths.soporteExterno
    ),
    soporteExternoObservaciones: normalizarTexto_(
      formData.soporteExternoObservaciones,
      REGLAS_FORMULARIO_.maxLengths.soporteExternoObservaciones
    ),
    resolvioNecesidad: normalizarTexto_(
      formData.resolvioNecesidad,
      REGLAS_FORMULARIO_.maxLengths.resolvioNecesidad
    ),
    estadoCaso,
    fechaCierre,
    expectativas: normalizarTexto_(
      formData.expectativas,
      REGLAS_FORMULARIO_.maxLengths.expectativas
    ),
    clasificacionTexto: normalizarTexto_(
      formData.clasificacionTexto,
      REGLAS_FORMULARIO_.maxLengths.clasificacionTexto
    ),
    clasificacionCeldas: normalizarTexto_(
      formData.clasificacionCeldas,
      REGLAS_FORMULARIO_.maxLengths.clasificacionCeldas
    ),
    fotosEvidencia: Array.isArray(formData.fotosEvidencia)
      ? formData.fotosEvidencia
      : [],
  };
}

function normalizarTexto_(value, maxLength) {
  const text = String(value || "").trim();
  const limitado = maxLength ? text.slice(0, maxLength) : text;
  return protegerFormula_(limitado);
}

function protegerFormula_(value) {
  if (/^[=+\-@]/.test(value)) {
    return `'${value}`;
  }
  return value;
}

function validarFormulario_(formData) {
  REGLAS_FORMULARIO_.required.forEach((campo) => {
    if (!formData[campo]) {
      const label = CAMPOS_LABEL_[campo] || campo;
      throw new Error(`Falta el campo obligatorio: ${label}.`);
    }
  });

  if (!/^\d{4}-\d{2}-\d{2}$/.test(formData.fechaAtencion)) {
    throw new Error("Fecha de atencion no valida.");
  }

  if (!/^\d{5,15}$/.test(formData.servidorCedula)) {
    throw new Error("Cedula del profesional no valida.");
  }

  if (!/^\d{5,15}$/.test(formData.tecnicoCedula)) {
    throw new Error("Cedula del tecnico no valida.");
  }

  if (formData.soporteExterno === "Sí" && !formData.soporteExternoObservaciones) {
    throw new Error("Indique observaciones de soporte externo.");
  }

  validarEstadoCaso_(formData.estadoCaso, formData.fechaCierre);

  validarClasificacion_(formData.clasificacionCeldas);
  validarFotos_(formData.fotosEvidencia);
}

function validarEstadoCaso_(estadoCaso, fechaCierre) {
  const validos = ["Abierto", "En proceso", "Cerrado"];
  if (!validos.includes(estadoCaso)) {
    throw new Error("Estado del caso no valido.");
  }
  if (estadoCaso === "Cerrado") {
    if (!fechaCierre) {
      throw new Error("Indique la fecha de cierre.");
    }
    if (!/^\d{4}-\d{2}-\d{2}$/.test(fechaCierre)) {
      throw new Error("Fecha de cierre no valida.");
    }
  }
}

function validarClasificacion_(value) {
  if (!value) {
    return;
  }
  const codigos = String(value)
    .split(",")
    .map((item) => item.trim())
    .filter(Boolean);
  if (!codigos.length) {
    return;
  }
  const regex = /^(C(1[4-9]|2\d|3[0-6])|F(1[4-9]|2\d|3[0-4]))$/;
  codigos.forEach((codigo) => {
    if (!regex.test(codigo)) {
      throw new Error("Clasificacion no valida.");
    }
  });
}

function validarFotos_(fotos) {
  if (!Array.isArray(fotos)) {
    return;
  }
  if (fotos.length > REGLAS_FORMULARIO_.maxFotos) {
    throw new Error(`Maximo ${REGLAS_FORMULARIO_.maxFotos} fotos permitidas.`);
  }
  fotos.forEach((foto, index) => {
    if (!foto || !foto.data) {
      throw new Error(`Foto ${index + 1} no valida.`);
    }
    if (
      foto.mimeType &&
      !REGLAS_FORMULARIO_.allowedMimeTypes.includes(foto.mimeType)
    ) {
      throw new Error(`Formato no permitido en foto ${index + 1}.`);
    }
    const bytes = Math.floor((String(foto.data).length * 3) / 4);
    if (bytes > REGLAS_FORMULARIO_.maxFotoBytes) {
      const maxMb = Math.floor(REGLAS_FORMULARIO_.maxFotoBytes / (1024 * 1024));
      throw new Error(`Foto ${index + 1} supera ${maxMb} MB.`);
    }
  });
}

function formatearValor_(value) {
  if (value === null || value === undefined || value === "") {
    return "-";
  }
  return String(value);
}

function escapeHtml_(value) {
  return String(value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
}

function obtenerUltimoIdDesdeHoja_() {
  const sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName(
    "Registro de Solicitudes"
  );
  if (!sheet) {
    return 0;
  }

  const lastRow = sheet.getLastRow();
  if (lastRow < 1) {
    return 0;
  }

  const values = sheet.getRange(1, 1, lastRow, 1).getValues().flat();
  for (let i = values.length - 1; i >= 0; i -= 1) {
    const numero = extraerNumeroId_(values[i]);
    if (numero) {
      return numero;
    }
  }
  return 0;
}

function extraerNumeroId_(value) {
  if (!value) {
    return 0;
  }
  const match = String(value).trim().match(/^CS-(\d{4})$/);
  if (!match) {
    return 0;
  }
  return parseInt(match[1], 10);
}

function formatearIdSolicitud_(numero) {
  return `CS-${String(numero).padStart(4, "0")}`;
}

function idYaExiste_(sheet, id) {
  const lastRow = sheet.getLastRow();
  if (lastRow < 1) {
    return false;
  }
  const finder = sheet
    .getRange(1, 1, lastRow, 1)
    .createTextFinder(id)
    .matchEntireCell(true)
    .findNext();
  return Boolean(finder);
}
