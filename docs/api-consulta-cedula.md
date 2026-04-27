# API Coosalud — Consulta de Afiliado por Cédula

## Descripción

Retorna el **historial completo** de consultas realizadas a Coosalud para un número de cédula específico, ordenado del registro **más reciente al más antiguo**. Solo se incluyen consultas exitosas.

---

## Endpoint

```
GET /api/consulta/cedula/{cedula}
```

### Parámetros de ruta

| Parámetro | Tipo   | Requerido | Descripción                     |
|-----------|--------|-----------|---------------------------------|
| `cedula`  | string | Sí        | Número de cédula (solo dígitos) |

### Autenticación

Requiere token **Bearer** de Sanctum en el header de la petición.

```
Authorization: Bearer <token>
```

---

## Ejemplo de petición

```http
GET /api/consulta/cedula/1234567890
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
Accept: application/json
```

---

## Respuestas

### 200 — Consulta exitosa

```json
{
  "success": true,
  "message": "Consulta exitosa.",
  "total": 2,
  "data": [
    {
      "cedula": "1234567890",
      "tipo_documento": "CC",
      "primer_nombre": "JUAN",
      "segundo_nombre": "CARLOS",
      "primer_apellido": "PÉREZ",
      "segundo_apellido": "GÓMEZ",
      "nombre_completo": "JUAN CARLOS PÉREZ GÓMEZ",
      "departamento": "CÓRDOBA",
      "municipio": "MONTERÍA",
      "direccion": "CRA 5 # 10-20",
      "regimen": "SUBSIDIADO",
      "estado_afiliado": "ACTIVO",
      "sede": "MONTERÍA CENTRO",
      "ips": "ESE HOSPITAL SAN JERÓNIMO",
      "celular": "3001234567",
      "telefono_fijo": "7891234",
      "correo": "juan.perez@correo.com",
      "poblacion_especial": null,
      "grupo_etnico": null,
      "consultado_en": "2026-04-27T10:30:00+00:00"
    },
    {
      "cedula": "1234567890",
      "tipo_documento": "CC",
      "primer_nombre": "JUAN",
      "segundo_nombre": "CARLOS",
      "primer_apellido": "PÉREZ",
      "segundo_apellido": "GÓMEZ",
      "nombre_completo": "JUAN CARLOS PÉREZ GÓMEZ",
      "departamento": "CÓRDOBA",
      "municipio": "MONTERÍA",
      "direccion": "CRA 5 # 10-20",
      "regimen": "SUBSIDIADO",
      "estado_afiliado": "ACTIVO",
      "sede": "MONTERÍA NORTE",
      "ips": "ESE HOSPITAL SAN JERÓNIMO",
      "celular": "3001234567",
      "telefono_fijo": null,
      "correo": null,
      "poblacion_especial": null,
      "grupo_etnico": null,
      "consultado_en": "2026-03-10T08:00:00+00:00"
    }
  ]
}
```

### 404 — Sin resultados

```json
{
  "success": false,
  "message": "No se encontraron resultados para la cédula proporcionada.",
  "data": null
}
```

---

## Descripción de campos del JSON de respuesta

### Nivel raíz

| Campo     | Tipo    | Descripción                                                    |
|-----------|---------|----------------------------------------------------------------|
| `success` | boolean | `true` si la operación fue exitosa, `false` en caso contrario |
| `message` | string  | Mensaje descriptivo del resultado                              |
| `total`   | integer | Cantidad total de registros retornados                         |
| `data`    | array   | Arreglo de objetos con el historial de consultas               |

### Objeto dentro de `data[]`

| Campo               | Tipo            | Descripción                                                                        |
|---------------------|-----------------|------------------------------------------------------------------------------------|
| `cedula`            | string          | Número de documento del afiliado                                                   |
| `tipo_documento`    | string / null   | Tipo de documento (ej. `CC`, `TI`, `CE`, `PA`)                                    |
| `primer_nombre`     | string / null   | Primer nombre del afiliado                                                         |
| `segundo_nombre`    | string / null   | Segundo nombre del afiliado. Puede ser `null`                                      |
| `primer_apellido`   | string / null   | Primer apellido del afiliado                                                       |
| `segundo_apellido`  | string / null   | Segundo apellido del afiliado. Puede ser `null`                                    |
| `nombre_completo`   | string / null   | Nombre completo concatenado tal como aparece en Coosalud                           |
| `departamento`      | string / null   | Departamento de residencia registrado                                              |
| `municipio`         | string / null   | Municipio de residencia registrado                                                 |
| `direccion`         | string / null   | Dirección de residencia registrada                                                 |
| `regimen`           | string / null   | Régimen de salud (ej. `SUBSIDIADO`, `CONTRIBUTIVO`)                                |
| `estado_afiliado`   | string / null   | Estado actual del afiliado (ej. `ACTIVO`, `RETIRADO`, `SUSPENDIDO`)                |
| `sede`              | string / null   | Sede de Coosalud asignada al afiliado                                              |
| `ips`               | string / null   | IPS primaria asignada al afiliado                                                  |
| `celular`           | string / null   | Número de celular de contacto registrado                                           |
| `telefono_fijo`     | string / null   | Teléfono fijo de contacto. Puede ser `null`                                        |
| `correo`            | string / null   | Correo electrónico de contacto. Puede ser `null`                                   |
| `poblacion_especial`| string / null   | Indica si el afiliado pertenece a alguna población especial. `null` si no aplica   |
| `grupo_etnico`      | string / null   | Grupo étnico del afiliado si aplica. `null` si no aplica                           |
| `consultado_en`     | string ISO 8601 | Fecha y hora en que se realizó la consulta (UTC)                                   |

---

## Notas

- Los registros se ordenan de **más reciente a más antiguo** según el campo `consultado_en`.
- Solo se retornan consultas marcadas como exitosas (`status = 'success'`).
- Si la cédula no tiene registros exitosos en la base de datos, se retorna HTTP `404`.
- El campo `cedula` en la URL solo acepta dígitos numéricos; cualquier otro carácter retorna `404` automáticamente.
