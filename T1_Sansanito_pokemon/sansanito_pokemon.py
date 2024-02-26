import pyodbc
import os.path
from random import randint

def get_odbc_pilot():
    '''
    Funcion: obtener el nombre del controlador ODBC
    Inputs:
        Ninguno
    Returns:
        string driver = nombre del controlador ODBC
    '''
    drivers = pyodbc.drivers()
    for driver in drivers:
        if 'OraDB' in driver:
            return driver
    print("Error. No se encontro el controlador ODBC")
    return None

def connect_to_db(db, uid, pw):
    '''
    Funcion: conectarse a la base de datos
    Inputs:
        string db = nombre de la base de datos
        string uid = nombre de usuario
        string pw = contraseña del usuario
    Returns:
        pyodbc.Connection object conn = objeto de conexion de PYODBC
    '''
    try:
        driver = get_odbc_pilot()
        conn = pyodbc.connect("DRIVER="+driver+";DBQ="+db+";Uid="+uid+";Pwd="+pw)
        return conn
    except pyodbc.Error as e:
        print("Error al conectar a la base de datos:", e)
        raise


def create_tables(conn):
    '''
    Funcion: crear tablas POKEMON (POYO) y SANSANITO
    Inputs:
        pyodbc.Connection object conn = objeto de conexion de PYODBC
    Returns:
        Ninguno
    '''
    cursor = conn.cursor()

    #Compruebo si las tablas ya estan creadas
    if (cursor.execute("SELECT * FROM tab WHERE tname = 'POKEMON';").fetchall()):
        #En caso de existir POKEMON, la elimino
        print("Tabla POKEMON ya existe, eliminando...")
        cursor.execute("DROP TABLE POKEMON;")
        conn.commit()
        print("Tabla POKEMON eliminada satisfactoriamente")
        
    if (cursor.execute("SELECT * FROM tab WHERE tname = 'SANSANITO';").fetchall()):
        #En caso de existir SANSANITO, la elimino
        print("Tabla SANSANITO ya existe, eliminando...")
        cursor.execute("DROP TABLE SANSANITO;")
        conn.commit()
        print("Tabla SANSANITO eliminada satisfactoriamente")
        
    #Creo las tablas
    print("Creando tabla POKEMON...")
    cursor.execute("CREATE TABLE POKEMON(pokedex INT NOT NULL, nombre VARCHAR(50) PRIMARY KEY, tipo1 VARCHAR(50) NOT NULL, tipo2 VARCHAR(50), max_hp INT NOT NULL, legendario VARCHAR(50) NOT NULL);")
    conn.commit()
    print("Tabla POKEMON creada satisfactoriamente")
    
    print("Creando tabla SANSANITO...")
    cursor.execute("CREATE TABLE SANSANITO(id INT PRIMARY KEY, pokedex INT NOT NULL, nombre VARCHAR(50) NOT NULL, tipo1 VARCHAR(50) NOT NULL, tipo2 VARCHAR(50), current_hp INT NOT NULL, max_hp INT NOT NULL, legendario VARCHAR(50) NOT NULL, estado VARCHAR(50), ingreso DATE NOT NULL, prioridad INT NOT NULL);")
    conn.commit()
    print("Tabla SANSANITO creada satisfactoriamente")


def create_triggers_views(conn):
    '''
    Funcion: crear trigger para que el ID del pokemon internado sea auto-incrementable y crear la view para ver la tabla SANSANITO
    Inputs:
        pyodbc.Connection object conn = objeto de conexion de PYODBC
    Returns:
        Ninguno
    '''
    cursor = conn.cursor()

    #Compruebo si la secuencia ya esta creada
    if (cursor.execute("SELECT * FROM USER_SEQUENCES WHERE sequence_name = 'GENERAR_ID';").fetchall()):
        #En caso de existir, la elimino
        print("Secuencia GENERAR_ID ya existe, eliminando...")
        cursor.execute("DROP SEQUENCE GENERAR_ID;")
        conn.commit()
        print("Secuencia GENERAR_ID eliminada satisfactoriamente")

    #Compruebo si la vista ya esta creada
    if (cursor.execute("SELECT * FROM SYS.ALL_VIEWS WHERE view_name = 'PACIENTES';").fetchall()):
        #En caso de existir, la elimino
        print("Vista PACIENTES ya existe, eliminando...")
        cursor.execute("DROP VIEW PACIENTES;")
        conn.commit()
        print("Vista PACIENTES eliminada satisfactoriamente")

    #Creo la secuencia, el disparador y la vista
    print("Creando secuencia GENERAR_ID...")
    cursor.execute("CREATE SEQUENCE GENERAR_ID START WITH 1 INCREMENT BY 1 CACHE 100;")
    conn.commit()
    print("Secuencia GENERAR_ID creada satisfactoriamente")

    print("Creando disparador IDENTIFICADOR...")
    cursor.execute("CREATE OR REPLACE TRIGGER IDENTIFICADOR BEFORE INSERT ON SANSANITO FOR EACH ROW BEGIN:new.ID := GENERAR_ID.nextval;END;")
    conn.commit()
    print("Disparador IDENTIFICADOR creado satisfactoriamente")

    print("Creando vista PACIENTES...")
    cursor.execute("CREATE VIEW PACIENTES AS SELECT id, nombre, current_hp, max_hp, legendario, prioridad FROM SANSANITO;")
    conn.commit()
    print("Vista PACIENTES creada satisfactoriamente")


def import_data(filename, separator, conn):
    '''
    Funcion: importar datos de pokemons desde un archivo csv a la tabla POKEMON
    Inputs:
        string filename = nombre del archivo csv
        string separator = separador de cada dato en el archivo
        pyodbc.Connection object conn = objeto de conexion de PYODBC
    Returns:
        bool True = en caso de importar el archivo satisfactoriamente
        bool False = en caso de error
    '''
    i = 0
    cursor = conn.cursor()

    print("Importando datos desde "+filename+"...")
    #Intento abrir el archivo csv. En caso de error, muestro un mensaje y termino el programa
    if not (os.path.isfile(filename)):
        print("Error. El archivo "+filename+" no existe")
        return False

    #En caso exitoso, intento leer cada linea
    file = open(filename,'r')
    for line in file:
        #Si el separador señalado no corresponde, muestro un mensaje y termino el programa
        if (len(line.split(separator)) == 1):
            print("Error. El archivo no usa '"+separator+"' como separador")
            return False
        
        #En caso contrario, empiezo a leer cada linea del archivo csv y lo importo a la tabla POKEMON. El primero se omite ya que es el encabezado
        if (i==0):
            i+=1
        else:
            pokedex,nombre,tipo1,tipo2,_,max_hp,_,_,_,_,_,_,legendario = line.strip().split(separator)
            cursor.execute("INSERT INTO pokemon (pokedex, nombre, tipo1, tipo2, max_hp, legendario) values(?,?,?,?,?,?);",(pokedex, nombre, tipo1, tipo2, max_hp, legendario))
            conn.commit()
            i+=1

    #Cierro el archivo
    file.close()
    print(str(i-1)+" pokemons importados satisfactoriamente")
    print("----------------------")
    return True


def menu_usuario(conn):
    '''
    Funcion: desplegar al usuario un menu de interaccion, para manipular la tabla SANSANITO
    Inputs:
        pyodbc.Connection object conn = objeto de conexion de PYODBC
    Returns:
        Ninguno
    '''
    opcion = "-1"
    print("Bienvenido al Sansanito Pokemon")
    
    #Muestro las opciones y le pido al usuario ingresar una
    while (opcion != "0"): 
        print("¿Que desea hacer?\n1.Ingresar un pokemon\n2.Ver los 10 pokemons con mayor prioridad\n3.Ver los 10 pokemons con menor prioridad\n4.Ver los pokemons con un estado especifico\n5.Ver los pokemons legendarios\n6.Ver el pokemon que lleva mas tiempo internado\n7.Ver el nombre pokemon mas repetido\n8.Ver lista de pokemons internados\n9.Operaciones CRUD\n0.Salir")
        opcion = input("Ingrese opcion: ")
        print("----------------------")

        #En caso de que ingrese una opcion incorrecta, muestro un mensaje y vuelvo a preguntar
        while (not opcion.isnumeric()) or (len(opcion)!=1) or (int(opcion)<0):
            print("Opcion incorrecta, intente nuevamente")
            opcion = input("Ingrese opcion: ")
            print("----------------------")

        #En caso de ser una entrada valida, ejecuto la opcion correspondiente
        if opcion == "1":
            ingresar_pokemon(conn)
        elif opcion == "2":
            filter_by(conn, 'prioridad', 'desc', 10)
        elif opcion == "3":
            filter_by(conn, 'prioridad', 'asc', 10)
        elif opcion == "4":
            filter_by(conn, 'estado')
        elif opcion == "5":
            filter_by(conn, 'legendario')
        elif opcion == "6":
            filter_by(conn, 'ingreso', 'asc', 1)
        elif opcion == "7":
            filter_by(conn, 'nombre', 'desc', 1)
        elif opcion == "8":
            view(conn)
        elif opcion == "9":
            crud_op(conn)
        elif opcion == "0":
            print("Hasta luego")


def ingresar_pokemon(conn):
    '''
    Funcion: insertar un pokemon a la tabla SANSANITO
    Inputs:
        pyodbc.Connection object conn = objeto de conexion de PYODBC
    Returns:
        Ninguno
    '''
    #Pregunto si desea insertar un pokemon a la tabla SANSANITO de forma manual o muchos automaticamente
    print("¿Que desea hacer?\n1.Ingresar un pokemon por su nombre\n2.Ingresar aleatoriamente muchos pokemons\n0.Regresar")
    opcion = input("Ingrese opcion: ")
    print("----------------------")

    #En caso de que ingrese una opcion incorrecta, muestro un mensaje y vuelvo a preguntar
    while ((opcion != "0") and (opcion != "1") and (opcion !="2")):
        print("Opcion incorrecta, intente nuevamente")
        opcion = input("Ingrese opcion: ")
        print("----------------------")

    #Opcion 1: Insertar un pokemon manualmente. Se solicitan los datos necesarios para la tabla SANSANITO
    if (opcion == "1"):
        cursor = conn.cursor()
        
        #Solicito el nombre del pokemon
        nombre = input("Ingrese nombre del pokemon: ")

        #Se verifica si el nombre del pokemon es valido
        row = cursor.execute("SELECT * FROM pokemon where nombre = ?", nombre).fetchone()

        #Si el pokemon no existe, o bien es legendario y ya ha sido internado uno igual, muestro un mensaje y vuelvo a preguntar
        while (not row or hay_legendario(nombre)):
            print("Error. El pokemon "+nombre+" no existe o ya hay un legendario ingresado con ese nombre. Intente nuevamente")
            nombre = input("Ingrese nombre del pokemon: ")
            row = cursor.execute("SELECT * FROM pokemon where nombre = ?", nombre).fetchone()
            
        #Desempaqueto los datos del pokemon
        pokedex = row[0]
        tipo1 = row[2]
        tipo2 = row[3]
        max_hp = row[4]
        legendario = row[5]

        #Solicito la vida actual del pokemon
        current_hp = input("Ingrese vida actual de su pokemon: ")

        #Si la entrada es invalida, muestro un error y vuelvo a preguntar
        while (not current_hp.isnumeric()) or (int(current_hp)<0) or (int(current_hp) >= max_hp):
            print("Error. Numero no valido. La vida minima de tu pokemon es 0 y la maxima de tu pokemon es "+str(max_hp)+". Intente nuevamente")
            current_hp = input("Ingrese vida actual de su pokemon: ")

        #Solicito el estado actual del pokemon
        estados = ['','Envenenado', 'Paralizado', 'Quemado', 'Dormido', 'Congelado']
        estado = input("Ingrese estado de su pokemon: ")
        #Si la entrada es invalida, muestro un error y vuelvo a preguntar
        while estado not in estados:
            print("Error. Estado no valido. Los estados posibles son "+str(estados)+". Intente nuevamente")
            estado = input("Ingrese estado de su pokemon: ")
            
        print("----------------------")
        #Genero la fecha de ingreso y prioridad
        ingreso = obtener_fecha(conn)
        current_hp = int(current_hp)
        prioridad = max_hp - current_hp
        if (estado != ''):
            prioridad*=10

        #Calculo el tamaño que ocupara en la tabla
        if (legendario == "True"):
            tamaño = 5
        else:
            tamaño = 1

        #Si su ingreso sobrepasa la capacidad, veo si puedo hacerle un cupo
        capacidad = capacidad_actual(conn)
        if (capacidad+tamaño > 50):
            admitido = buscar_cupo(conn, prioridad, legendario)

        #Si no hay sobrecupo, siempre estara admitido
        else:
            admitido = True

        #Si fue posible, se ingresa el pokemon
        if (admitido):
            cursor.execute("INSERT INTO sansanito (pokedex, nombre, tipo1, tipo2, current_hp, max_hp, legendario, estado, ingreso, prioridad) values(?,?,?,?,?,?,?,?,?,?);",(pokedex, nombre, tipo1, tipo2, current_hp, max_hp, legendario, estado, ingreso, prioridad))
            conn.commit()
            print("1 pokemon ingresado satisfactoriamente")
            
        #En caso contrario, muestro un error
        else:
            print("Lo siento, no hay cupo")

        print("La capacidad actual del sansanito es de "+str(capacidad_actual(conn))+"/50")
        print("----------------------")

    #Opcion 2: Insertar una cantidad arbitraria de pokemons automaticamente                   
    elif (opcion == "2"):
        #Solicito la cantidad. Si ingresa una entrada invalida muestro un error y vuelvo a preguntar
        cant = input("Ingrese cantidad: ")
        while (not cant.isnumeric()) or (int(cant)<=0):
            print("Cantidad incorrecta, ingrese un numero valido")
            cant = input("Ingrese cantidad: ")

        print("----------------------")
        #Llamo a la funcion auxiliar
        random_fill(conn, int(cant))
        print("----------------------")


def random_fill(conn, cant):
    '''
    Funcion: ingresar una cantidad arbitraria de pokemons a la tabla SANSANITO automaticamente
    Inputs:
        pyodbc.Connection object conn = objeto de conexion de PYODBC
        int cant = cantidad de pokemons que se desea insertar a la tabla SANSANITO
    Returns:
        Ninguno
    '''
    #Veo todos los pokemons disponibles de la tabla POKEMON
    cursor = conn.cursor()
    rows = cursor.execute("SELECT * FROM pokemon").fetchall()
    admitidos = 0

    #Selecciono aleatoriamente un pokemon
    for i in range(0, cant):
        dice = randint(0,len(rows))

        #Si ya hay un legendario con el mismo nombre ingresado, vuelvo a buscar
        while (hay_legendario(rows[dice][1])):
            dice = randint(0,len(rows))

        #Desempaqueto los datos del pokemon
        pokedex = rows[dice][0]
        nombre = rows[dice][1]
        tipo1 = rows[dice][2]
        tipo2 = rows[dice][3]
        max_hp = rows[dice][4]
        legendario = rows[dice][5]

        estados = ['','Envenenado', 'Paralizado', 'Quemado', 'Dormido', 'Congelado']
        estado = estados[randint(0,len(estados)-1)]
        
        #Genero la fecha de ingreso y prioridad
        ingreso = obtener_fecha(conn)
        current_hp = randint(0,max_hp-1)
        prioridad = max_hp - current_hp
        if (estado != ''):
            prioridad*=10

        #Calculo el tamaño que ocupara en la tabla
        if (legendario == "True"):
            tamaño = 5
        else:
            tamaño = 1

        #Si su ingreso sobrepasa la capacidad, veo si puedo hacerle un cupo
        capacidad = capacidad_actual(conn)
        if (capacidad+tamaño > 50):
            admitido = buscar_cupo(conn, prioridad, legendario)

        #Si no hay sobrecupo, siempre estara admitido
        else:
            admitido = True

        #Si fue posible, se ingresa el pokemon
        if (admitido):
            cursor.execute("INSERT INTO sansanito (pokedex, nombre, tipo1, tipo2, current_hp, max_hp, legendario, estado, ingreso, prioridad) values(?,?,?,?,?,?,?,?,?,?);",(pokedex, nombre, tipo1, tipo2, current_hp, max_hp, legendario, estado, ingreso, prioridad))
            conn.commit()
            admitidos += 1

        #En caso contrario, muestro un error y retorno
        else:
            print("Lo siento, no hay cupo")
        
    print(str(admitidos)+" pokemons ingresados satisfactoriamente")
    print("La capacidad actual del sansanito es de "+str(capacidad_actual(conn))+"/50")

        
def hay_legendario(nombre):
    '''
    Funcion: verificar si un pokemon legendario ya ha sido insertado a la tabla SANSANITO
    Inputs:
        string nombre = nombre del pokemon
    Returns:
        boolean True = en caso de que ya existiera un legendario con el mismo nombre
        boolean False = en caso contrario
    '''
    cursor = conn.cursor()
    #Verifico que exista un legendario del mismo nombre en la tabla SANSANITO
    pokemon = cursor.execute("SELECT * FROM sansanito WHERE legendario = 'True' AND nombre = ?;", nombre).fetchone()
    if (not pokemon):
        return False
    return True

        
def obtener_fecha(conn):
    '''
    Funcion: generar fecha de ingreso de un pokemon a la tabla SANSANITO
    Inputs:
        pyodbc.Connection object conn = objeto de conexion de PYODBC
    Returns:
        class 'datetime.datetime' object row[0] = objeto de tipo fecha
        
    '''
    cursor = conn.cursor()
    row = cursor.execute("SELECT sysdate FROM dual").fetchone()
    return row[0]


def buscar_cupo(conn, prioridad, legendario):
    '''
    Funcion: intenta buscar un cupo para el pokemon al eliminar de la tabla SANSANITO el/los pokemon/s con menor prioridad
    Inputs:
        pyodbc.Connection object conn = objeto de conexion de PYODBC
        int prioridad = prioridad del pokemon que busca cupo
        string legendario = si el pokemon que busca cupo es legendario
    Returns:
        bool True = si pudo hacer espacio
        bool False = en caso contrario
    '''  
    cursor = conn.cursor()
    #Reviso las prioridades de los pokemons en la tabla SANSANITO
    pokemon = cursor.execute("SELECT id, nombre, prioridad FROM sansanito WHERE legendario = ? ORDER BY prioridad ASC;", legendario).fetchone()

    #Verifico que haya un pokemon (legendario en el caso correspondiente) con menor prioridad que el postulante
    if (pokemon and prioridad>int(pokemon[2])):
        #Obtengo su id
        pok_id = pokemon[0]
        nombre = pokemon[1]

        #Finalmente lo elimino de la tabla y retorno True
        cursor.execute("DELETE FROM sansanito WHERE id = ?;", pok_id)
        conn.commit()
        print("Pokemon "+nombre+" eliminado para obtener cupo")
        return True

    #En caso contrario, retorno False
    else:
        return False


def filter_by(conn, column, order = "desc", cant = -1):
    '''
    Funcion: imprimir los pokemons que cumplan con los filtros solicitados
    Inputs:
        pyodbc.Connection object conn = objeto de conexion de PYODBC
        string column = filtro deseado
        string order = orden deseado (ascendente/descendente)
        int cant = x para ver una cantidad especifica x
                  -1 para verlos todos  
    Returns:
        Ninguno
    '''
    cursor = conn.cursor()
    select = "SELECT id, nombre FROM sansanito "
    valor = "NULL"
    
    #Filtrar por condiciones
    if (column == "estado") or (column == "legendario") or (column == "nombre"):
        #Si es por un estado, lo solicito
        if (column == "estado"):
            valor = input("Ingrese estado a consultar: ")
            print("----------------------")

        #Para los legendarios, lo fijo automaticamente
        elif (column == "legendario"):
            valor = "True"

        #Si desea ver el nombre del pokemon mas repetido, cuento las apariciones
        elif (column == "nombre"):
            repetidos = cursor.execute("SELECT nombre, COUNT(nombre) FROM sansanito GROUP BY nombre HAVING COUNT(nombre)>1 ORDER BY COUNT(nombre) DESC;").fetchone()

            #Si hay repetidos, guardo el nombre del que tiene mayor repeticion
            if (repetidos):
                valor = repetidos[0]

        #En cualquier caso, escribo la query
        where = "WHERE "+column+"='"+valor+"';"

        if (valor == ""):
            where = "WHERE estado IS NULL;"
            
        query = select+where
        
    #Filtrar por orden
    else:
        order = "ORDER BY "+column+" "+order+";"
        query = select+order
    
    #Busco los pokemons que cumplan los filtros
    pokemons = cursor.execute(query).fetchall()

    #Si no encuentra
    if (not pokemons):
        print("Lo siento, no hay pokemons que cumplan lo solicitado")
        print("----------------------")
        return 

    resultados = ""
    #Si encuentra y el usuario quiere verlos todos
    if (cant == -1):
        #Calculo el espaciado entre las columnas y muestro los datos
        print("ID                        NOMBRE")
        for pokemon in pokemons:
            for dato in pokemon:
                if (dato == ""):
                    dato = 'None'
                    
                espacio = calcular_espacio(dato)
                resultados += str(dato)
                resultados += espacio
            resultados += "\n"

    #Si encuentra y el usuario quiere ver una cantidad especifica 
    else:
        #En caso de que no hayan suficientes para mostrar, muestra los maximos posible
        cant_maxima = len(cursor.execute("SELECT * FROM sansanito;").fetchall())
        if (cant_maxima < cant):
            cant = cant_maxima

        #Calculo el espaciado entre las columnas y muestro los datos
        print("ID                        NOMBRE")
        for i in range(0, cant):
            for dato in pokemons[i]:
                if (dato == ""):
                    dato = 'None'
                    
                espacio = calcular_espacio(dato)
                resultados += str(dato)
                resultados += espacio            
            resultados += "\n"
            
    print(resultados)
    print("----------------------")


def view(conn):
    '''
    Funcion: mostrar view
    Inputs:
        pyodbc.Connection object conn = objeto de conexion de PYODBC
    Returns:
        Ninguno
    '''
    cursor = conn.cursor()

    #Selecciono todos los datos de la view
    pokemons = cursor.execute("SELECT * FROM pacientes ORDER BY prioridad DESC").fetchall()

    #Si el sansanito esta vacio, muestro un mensaje
    if (not pokemons):
        print("Lo siento, no hay pokemons que cumplan lo solicitado")
        print("----------------------")
        return

    #En caso contrario calculo el espaciado entre las columnas y muestro los datos
    resultados = ""
    print("ID                        NOMBRE                    CURRENT_HP                MAX_HP                    LEGENDARIO                PRIORIDAD")
    for pokemon in pokemons:
            for dato in pokemon:
                if (dato == ""):
                    dato = 'None'
                    
                espacio = calcular_espacio(dato)
                resultados += str(dato)
                resultados += espacio
            resultados += "\n"

    print(resultados)
    print("----------------------")
    
    
def crud_op(conn):
    '''
    Funcion: mostrar sub-menu para las operaciones CRUD
    Inputs:
        pyodbc.Connection object conn = objeto de conexion de PYODBC
    Returns:
        Ninguno
    '''
    #Muestro las opciones y le pido al usuario elegir una
    print("¿Que desea hacer?\n1.Create\n2.Read\n3.Update\n4.Delete\n0.Regresar")
    opcion = input("Ingrese opcion: ")
    print("----------------------")

    #En caso de que ingrese una opcion incorrecta, muestro un mensaje y vuelvo a preguntar
    while ((opcion != "0") and (opcion != "1") and (opcion !="2") and (opcion !="3") and (opcion !="4")):
        print("Opcion incorrecta, intente nuevamente")
        opcion = input("Ingrese opcion: ")
        print("----------------------")

    #Opcion 1: Create. Se solicitan los datos necesarios para insertar un pokemon en la tabla SANSANITO
    if (opcion == "1"):
        cursor = conn.cursor()
        
        #Solicito el nombre del pokemon
        nombre = input("Ingrese nombre del pokemon: ")

        #Se verifica si el nombre del pokemon es valido
        row = cursor.execute("SELECT * FROM pokemon where nombre = ?", nombre).fetchone()

        #Si el pokemon no existe, o bien es legendario y ya ha sido internado uno igual, muestro un mensaje y vuelvo a preguntar
        while (not row or hay_legendario(nombre)):
            print("Error. El pokemon "+nombre+" no existe o ya hay un legendario ingresado con ese nombre. Intente nuevamente")
            nombre = input("Ingrese nombre del pokemon: ")
            row = cursor.execute("SELECT * FROM pokemon where nombre = ?", nombre).fetchone()
            
        #Desempaqueto los datos del pokemon
        pokedex = row[0]
        tipo1 = row[2]
        tipo2 = row[3]
        max_hp = row[4]
        legendario = row[5]

        #Solicito la vida actual del pokemon
        current_hp = input("Ingrese vida actual de su pokemon: ")

        #Si la entrada es invalida, muestro un error y vuelvo a preguntar
        while (not current_hp.isnumeric()) or (int(current_hp)<0) or (int(current_hp) >= max_hp):
            print("Error. Numero no valido. La vida maxima de tu pokemon es "+str(max_hp)+". Intente nuevamente")
            current_hp = input("Ingrese vida actual de su pokemon: ")

        #Solicito el estado actual del pokemon
        estados = ['','Envenenado', 'Paralizado', 'Quemado', 'Dormido', 'Congelado']
        estado = input("Ingrese estado de su pokemon: ")
        #Si la entrada es invalida, muestro un error y vuelvo a preguntar
        while estado not in estados:
            print("Error. Estado no valido. Intente nuevamente")
            estado = input("Ingrese estado de su pokemon: ")

        print("----------------------")
        #Genero la fecha de ingreso y prioridad
        ingreso = obtener_fecha(conn)
        current_hp = int(current_hp)
        prioridad = max_hp - current_hp
        if (estado != ''):
            prioridad*=10

        #Calculo el tamaño que ocupara en la tabla
        if (legendario == "True"):
            tamaño = 5
        else:
            tamaño = 1

        #Si su ingreso sobrepasa la capacidad, veo si puedo hacerle un cupo
        capacidad = capacidad_actual(conn)
        if (capacidad+tamaño > 50):
            admitido = buscar_cupo(conn, prioridad, legendario)

        #Si no hay sobrecupo, siempre estara admitido
        else:
            admitido = True

        #Si fue posible, se ingresa el pokemon
        if (admitido):
            cursor.execute("INSERT INTO sansanito (pokedex, nombre, tipo1, tipo2, current_hp, max_hp, legendario, estado, ingreso, prioridad) values(?,?,?,?,?,?,?,?,?,?);",(pokedex, nombre, tipo1, tipo2, current_hp, max_hp, legendario, estado, ingreso, prioridad))
            conn.commit()
            print("1 pokemon ingresado satisfactoriamente")
            
        #En caso contrario, muestro un error
        else:
            print("Lo siento, no hay cupo")

        print("La capacidad actual del sansanito es de "+str(capacidad_actual(conn))+"/50")
        print("----------------------")

    #Opcion 2: Read de uno o mas pokemons de la tabla SANSANITO
    elif (opcion == "2"):
        cursor = conn.cursor()

        #Se solicitan las columnas que desea ver
        select = input("¿Que datos desea ver? Ingreselos separados por coma, o * si desea verlos todos: ")

        #En caso de que ingrese una opcion incorrecta, muestro un mensaje y vuelvo a preguntar
        while (select == "") or (select.isnumeric()):
            print("Error, entrada incorrecta. Intentelo nuevamente")
            select = input("¿Que datos desea ver? Ingreselos separados por coma, o * si desea verlos todos: ")

        #Parametros opcionales
        where = input("¿Alguna condicion que se deba cumplir? Ingreselas separadas por un operador logico (AND/OR) o deje vacio si no quiere un filtro: ")
        order = input("¿Algun orden en particular? Ingrese la columna y si desea verlo de forma ascendente o descendente, o deje vacio si no quiere un filtro: ")
        print("----------------------")
        
        #Ejecucion de la query
        if (where == "" and order == ""):
            query = "SELECT "+select+" FROM sansanito;"

        else:
            if (where == ""):
                query = "SELECT "+select+" FROM sansanito ORDER BY "+order+";"

            elif (order == ""):
                query = "SELECT "+select+" FROM sansanito WHERE "+where+";"

            else:
                query = "SELECT "+select+" FROM sansanito WHERE "+where+" ORDER BY "+order+";"

        #Busca los datos solicitados
        rows = cursor.execute(query).fetchall()

        #Si no encuentra muestra un mensaje
        if (not rows):
            print("Lo siento, no hay pokemons que cumplan lo solicitado")
            print("----------------------")
            return
        
        #En caso contrario, obtengo las columnas y calculo el espaciado entre ellas, luego imprimo los datos
        columnas = obtener_columnas(conn, select)
        encabezado = ""
        for columna in columnas:
            espacio = calcular_espacio(columna)
            encabezado += columna
            encabezado += espacio

        resultados = ""

        print(encabezado)
        for i in range (0, len(rows)):
            for dato in rows[i]:
                if (dato == ""):
                    dato = 'None'
                    
                espacio = calcular_espacio(dato)
                resultados += str(dato)
                resultados += espacio
            resultados += "\n"

        print(resultados)
        print("----------------------")
            
    #Opcion 3: Update un pokemon de la tabla SANSANITO. Solo se podra acceder mediante su ID
    elif (opcion == "3"):
        cursor = conn.cursor()
        pok_id = input("Ingrese id del pokemon: ")

        #Verifico que el pokemon exista
        row = cursor.execute("SELECT current_hp, max_hp, estado FROM sansanito where id = ?", pok_id).fetchone()

        #En caso contrario, muestro un error y vuelvo a preguntar
        while (not row):
            print("Error. El pokemon con id "+pok_id+" no existe. Intente nuevamente")
            pok_id = input("Ingrese id del pokemon: ")
            row = cursor.execute("SELECT current_hp, max_hp, estado FROM sansanito where id = ?", pok_id).fetchone()


        #Muestro los parametros actualizables y solicito una opcion
        print("¿Que desea hacer?\n1.Actualizar vida actual\n2.Actualizar estado\n0.Regresar")
        opcion = input("Ingrese opcion: ")
        print("----------------------")

        #Si la entrada es invalida, muestro un error y vuelvo a preguntar
        while ((opcion != "0") and (opcion != "1") and (opcion !="2")):
            print("Opcion incorrecta, intente nuevamente")
            opcion = input("Ingrese opcion: ")
            print("----------------------")

        #Actualizar vida actual
        if (opcion == "1"):
            max_hp = row[1]
            estado = row[2]

            #Si la entrada es invalida, muestro un error y vuelvo a preguntar
            current_hp = input("Ingrese nuevo valor para la vida actual de su pokemon: ")
            while (not current_hp.isnumeric()) or (int(current_hp)<0) or (int(current_hp) >= max_hp):
                print("Error. Numero no valido. La vida maxima de tu pokemon es "+str(max_hp)+". Intente nuevamente")
                current_hp = input("Ingrese nuevo valor para la vida actual de su pokemon: ")

            #Vuelvo a calcular la prioridad
            current_hp = int(current_hp)
            prioridad = max_hp - current_hp
            
            if (estado):
                prioridad*=10
            
            #Finalmente actualizo el pokemon
            cursor.execute("UPDATE sansanito SET current_hp = ?, prioridad = ? WHERE id = ?;", current_hp, prioridad, pok_id)
            conn.commit()
            print("Pokemon actualizado")
            print("----------------------")

        #Actualizar estado
        elif (opcion == "2"):
            current_hp = row[0]
            max_hp = row[1]
            estados = ['','Envenenado', 'Paralizado', 'Quemado', 'Dormido', 'Congelado']

            #Si la entrada es invalida, muestro un error y vuelvo a preguntar
            estado = input("Ingrese nuevo estado de su pokemon: ")
            while estado not in estados:
                print("Error. Estado no valido. Intente nuevamente")
                estado = input("Ingrese nuevo estado de su pokemon: ")

            #Vuelvo a calcular la prioridad
            current_hp = int(current_hp)
            prioridad = max_hp - current_hp
            
            if (estado != ''):
                prioridad*=10

            #Finalmente actualizo el pokemon
            cursor.execute("UPDATE sansanito SET estado = ?, prioridad = ? WHERE id = ?;", estado, prioridad, pok_id)
            conn.commit()
            print("Pokemon actualizado")
            print("----------------------")

    #Opcion 4: Delete. Solo se podra acceder mediante su ID
    elif (opcion == "4"):
        cursor = conn.cursor()
        pok_id = input("Ingrese id del pokemon: ")

        #Verifico que el pokemon exista
        row = cursor.execute("SELECT * FROM sansanito where id = ?", pok_id).fetchone()

        #En caso contrario, muestro un error y vuelvo a preguntar
        while (not row):
            print("Error. El pokemon con id "+pok_id+" no existe. Intente nuevamente")
            pok_id = input("Ingrese id del pokemon: ")
            row = cursor.execute("SELECT * FROM sansanito where id = ?", pok_id).fetchone()

        #Finalmente elimino al pokemon
        cursor.execute("DELETE FROM sansanito WHERE id =?;", pok_id)
        conn.commit()
        print("Pokemon eliminado")
        print("----------------------")


def obtener_columnas(conn, columns):
    '''
    Funcion: entregar una lista con las columnas validas solicitadas por el usuario
    Inputs:
        pyodbc.Connection object conn = objeto de conexion de PYODBC
    Returns:
        list columnas_match = lista con las columnas validas solicitadas por el usuario
    '''
    cursor = conn.cursor()
    columnas_disponibles = []
    columnas_match = []
    
    #Genero una lista con cada columna solicitada separada
    columnas_pedidas = columns.strip().split(",")
    

    #Veo las columnas disponibles de la tabla y las almaceno en columnas_disponibles
    query = cursor.execute("SELECT column_name FROM USER_TAB_COLUMNS WHERE table_name = 'SANSANITO';").fetchall()
    for tupla in query:
        columnas_disponibles.append(tupla[0])

    if (columnas_pedidas[0] == "*"):
        columnas_match = columnas_disponibles

    else:
        for columna in columnas_pedidas:
            if columna.strip().upper() in columnas_disponibles:
                columnas_match.append(columna.strip().upper())

    return columnas_match

        
def calcular_espacio(dato):
    '''
    Funcion: justificar la impresion de la tabla al equidistar cada valor entre si
    Inputs:
        string dato = dato a imprimir en la tabla
    Returns:
        string espacio*" " = string con los espacios necesarios
    '''
    espacio = abs(26 - len(str(dato)))
    return espacio*" "

    
def capacidad_actual(conn):
    '''
    Funcion: calcular la capacidad actual de la tabla SANSANITO
    Inputs:
        pyodbc.Connection object conn = objeto de conexion de PYODBC
    Returns:
        int capacidad = capacidad actual de la tabla SANSANITO
    '''
    capacidad = 0
    cursor = conn.cursor()

    #Verifico si existen legendarios. Su tamaño es 5
    legendarios = cursor.execute("SELECT * FROM sansanito WHERE legendario = 'True';").fetchall()
    capacidad += len(legendarios)*5

    #Verifico si existen no-legendarios. Su tamaño es 1
    normales = cursor.execute("SELECT * FROM sansanito WHERE legendario = 'False';").fetchall()
    capacidad += len(normales)
    
    return(capacidad)

user = 'system'
password = 'oracle'
conn = connect_to_db('XE',user,password)
create_tables(conn)
create_triggers_views(conn)
ready = import_data('pokemon.csv', ',', conn)
if (ready):
    menu_usuario(conn)




                
            
                
            
            
