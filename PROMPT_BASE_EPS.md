# Prompt Base para Consulta de Afiliados por EPS

Usa este prompt como plantilla para replicar el sistema con otra EPS. Solo cambia las secciones marcadas con `[CAMBIAR]`.

---

## Prompt

Crea una aplicación web Laravel para consultar afiliados de `[CAMBIAR: nombre de la EPS]`.

### Stack Tecnológico
- Backend: PHP 8.5+ / Laravel 13
- Base de datos: SQLite
- Import/Export Excel: Maatwebsite/Excel
- HTTP Client: Guzzle (Laravel HTTP)
- Frontend: Blade + CSS custom (tema oscuro glassmorphism) + JavaScript vanilla con fetch API
- Sin frameworks JS

### Funcionalidades
1. Login con roles: admin (acceso total) y consulta (solo buscar en historial y ver consultas completadas)
2. CRUD de usuarios (solo admin)
3. Subir archivo Excel/CSV con cédulas (solo admin)
4. Procesar cada cédula contra la API de la EPS mostrando resultados en tiempo real
5. Botón de reanudar si el proceso se interrumpe (continúa desde donde quedó)
6. Exportar resultados a Excel con estilos
7. Buscar por cédula en consultas anteriores (ambos roles)
8. Seeder con usuario admin y consulta para pruebas

### API de la EPS

`[CAMBIAR: toda esta sección con los datos de la nueva EPS]`

- URL: `https://puntofacilapi.coosalud.com/puntofacilback/api/Affiliate/afiliateByDoc`
- Método: POST
- Headers: Content-Type application/json
- Body: `{"documentNumber": "CEDULA", "documentType": "1"}`
- documentType siempre es "1" (cédula de ciudadanía)

Respuesta de ejemplo:
```json
{
  "success": true,
  "message": null,
  "data": {
    "identityNumber": "1001799934",
    "identityType": "CC",
    "email": null,
    "phone": "3044715029",
    "cityCode": "08560",
    "name": "JOSE JORGE CERVANTES MENDOZA",
    "address": "JORGE ELIECER GAITAN CL 17 17 06",
    "birthDate": "2002-06-20T00:00:00",
    "enabled": false,
    "ethnicGroup": "06",
    "specialPop": "5"
  }
}
```

`[CAMBIAR: pegar aquí la respuesta real de la API de la nueva EPS para que el parser se ajuste correctamente]`

### Campos a extraer del afiliado
Cédula, tipo documento, primer nombre, segundo nombre, primer apellido, segundo apellido, departamento, municipio, dirección, régimen, población especial, grupo étnico, paciente de riesgo, otros riesgos, celular, teléfono fijo, correo, estado del afiliado, sede, IPS.

`[CAMBIAR: agregar o quitar campos según lo que devuelva la API de la nueva EPS]`

### Arquitectura de archivos
```
app/
├── Http/Controllers/
│   ├── AuthController.php          # Login/logout
│   ├── UserController.php          # CRUD usuarios
│   └── ConsultaController.php      # Subir, procesar, exportar, buscar
├── Http/Middleware/
│   └── RoleMiddleware.php          # Control de acceso por rol
├── Services/
│   └── {NombreEps}Service.php      # Cliente HTTP contra la API
├── Imports/
│   └── CedulasImport.php           # Importador Excel (ToCollection)
├── Exports/
│   └── ResultsExport.php           # Exportador Excel con estilos
├── Models/
│   ├── User.php                    # Con campo role (admin/consulta)
│   ├── Consulta.php                # Lote de consulta
│   └── ConsultaResult.php          # Resultado individual
config/
└── {nombre_eps}.php                # URLs, delay, timeout
resources/views/
├── layouts/app.blade.php
├── auth/login.blade.php
├── users/index.blade.php
└── consultas/
    ├── index.blade.php             # Subir archivo + historial
    ├── process.blade.php           # Procesamiento en tiempo real
    ├── search.blade.php            # Buscar por cédula
    ├── show.blade.php              # Detalle de consulta
    └── files.blade.php             # Listar archivos exportables
```

### Notas importantes
- El procesamiento es secuencial vía AJAX (fetch): la vista llama a `/process-next` en loop, cada llamada procesa 1 cédula y devuelve el resultado
- Delay configurable entre peticiones para no saturar la API de la EPS
- Si el nombre viene en un solo campo (ej: "JOSE JORGE CERVANTES MENDOZA"), parsearlo en primer nombre, segundo nombre, primer apellido, segundo apellido
- La validación del archivo usa extensión (.xlsx, .xls, .csv) en vez de MIME type porque en Windows los CSV fallan con `mimes:`
- Crear seeder con: admin@{eps}.local / admin123 y consulta@{eps}.local / consulta123
- Crea todo tú, yo solo pruebo
