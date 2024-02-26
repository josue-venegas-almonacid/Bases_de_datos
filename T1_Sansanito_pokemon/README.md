# Nombre del Proyecto

Sansanito Pokémon

# Descripción

Sistema de ingreso de pokémones a centro de salud Sansanito mediante Oracle Database Express Edition y Python

# Herramientas necesarias

Se ha comprobado el correcto funcionamiento del proyecto con las siguientes versiones:
- Python 3.12.0
- Oracle Database 21c Express Edition
- SQL Developer 23.1.1

# Instalación
1. Descargar e instalar Python
2. Descargar e instalar Oracle Express Edition. Asegúrese de que la contraseña de acceso sea `oracle`
2. Instalar las librerías correspondientes:
- PyODBC: `python -m pip install pyodbc`
3. (Opcional) Descargar SQL Developer

# Uso
1. Clonar el repositorio
2. Acceder a la carpeta del repositorio
3. Abrir el terminal, ejecutar `python sansanito_pokemon.py` y navegar por las opciones
4. (Opcional) Puede utilizar SQL Developer para observar el comportamiento de la base de datos. Para esto, abra dicho programa y agregue una nueva conexión con los datos
- Nombre: `sansanito_pokemon`
- Usuario: `system`
- Contraseña: `oracle`