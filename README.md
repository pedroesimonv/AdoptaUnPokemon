# 🐾 Pokémon Rescue & Team Manager (MVP)

Este proyecto es una aplicación web de gestión de equipos de rescate y personal, desarrollada en PHP y MySQL. Aunque utiliza la temática Pokémon (vía PokeAPI), el núcleo del sistema es un **CRUD con Control de Acceso Basado en Roles (RBAC)** diseñado para coordinar misiones de emergencia.

## 🚀 Funcionalidades Clave
- **Autenticación Segura:** Sistema de login con cifrado BCRYPT.
- **Gestión de Roles:** Diferenciación entre Administrador (gestión total) y Rescatista (operaciones de campo).
- **Unidades Especializadas:** Creación de equipos por especialidad (Fuego, Agua, Sismos) y asignación de personal.
- **Bitácora de Salud:** Lógica de estados ("Herido" -> "Disponible") con registro histórico de altas médicas.
- **Integración de API:** Consumo de PokeAPI para automatizar la obtención de datos, imágenes y tipos.
- **Filtros Avanzados:** Panel de control con filtrado dinámico por estado y unidad asignada.

## 🛠️ Tecnologías Utilizadas
- **Backend:** PHP 8.x (Arquitectura modular con PDO).
- **Base de Datos:** MySQL / MariaDB.
- **Frontend:** Tailwind CSS (Diseño responsivo y Dark Mode).
- **API:** PokeAPI.

## ⚙️ Instalación en Local
1. Clona el repositorio: `git clone https://github.com/tu-usuario/adoptaUnPokemon.git`
2. Importa el archivo `/db/database.sql` en tu servidor MySQL (XAMPP).
3. Configura las credenciales en `config/Database.php`.
4. Mueve la carpeta a tu directorio `htdocs` y accede vía `localhost`.

## Puedes usar estar credenciales
Para admin: admin@admin.com
Pass: 123456
Para rescatista: benito@benito.com
Pass: 123456
---
*Proyecto con fines educativos sobre arquitectura de software y gestión de equipos.*