import gi
import threading
import urllib.request
import lcddriver
import json
import time
#display = lcddriver.lcd() 

gi.require_version("Gtk", "3.0")
from gi.repository import Gtk
from gi.repository import Gdk
from threading import Timer
from read import Rfid_PN532
#from adri import Rfid
#from pirc522 import RFID
import RPi.GPIO as GPIO




class Window(Gtk.Window):
    def __init__(self):
        Gtk.Window.__init__(self, title="CDR")
        self.set_position(Gtk.WindowPosition.CENTER)
        self.set_default_size(1000, 1500)
        self.set_border_width(10)

        self.create_pantalla1()
        
        thread_uid = threading.Thread(target=self.read_uid)  
        thread_uid.daemon = True
        thread_uid_in_use = False
        thread_uid.start()

        
        

        

        
    
        self.css_provider = Gtk.CssProvider() #Adding styles
        self.css_provider.load_from_path("estilos.css") 
        self.context = Gtk.StyleContext()
        self.screen = Gdk.Screen.get_default()
        self.context.add_provider_for_screen(self.screen, self.css_provider, Gtk.STYLE_PROVIDER_PRIORITY_APPLICATION)

        
    
        
    
        
    #Generem la funció del timer.
    def restart_timer(self):
        global t
        t = Timer(300,self.log_out)#5 minuts de timeout


#Interfície de la pantalla de log in:
        
    
    #Es crea la pantalla de login
    def create_pantalla1(self):
        self.pantalla1 = Gtk.Grid(column_homogeneous=True, row_homogeneous=True, column_spacing=10,row_spacing=250)
        self.add(self.pantalla1)
        
        self.box = Gtk.Box(orientation=Gtk.Orientation.VERTICAL, spacing=10)  #Create box inside window
        self.box.set_homogeneous(False)

        
        self.label = Gtk.Label(label="Please, login with your university card.")  #Create label
        self.label.set_name("label")
        self.box.pack_start(self.label, True, True, 0)
        self.box.set_vexpand(False)

        
        #login = Gtk.Button.new_with_label("Boton login (inutil)")
        #login.connect("clicked", self.log_in)
        
        #Adding box to window
        self.pantalla1.attach(self.box,0,1,1,1)
        #self.pantalla1.attach(login,0,0,1,1)
        

        #display.lcd_clear()
        #display.lcd_display_string('Please, login with', 2)
        #display.lcd_display_string('your university card', 3)

        self.pantalla1.show_all()
        
        
        
        
    

#Funcions associades a la pantalla de log in:

    def read_uid(self):
            
        rf = Rfid_PN532()
        uid = rf.read_uid()
        print(uid);
        if(uid=="CBAC3D96"):
            uid = "890C769C"
        #uid = "890C769C"
        threading.Thread(target=self.log_in, args=(uid,), daemon = True).start()
        
    #Funcio login
    #Si reconeix el uid passa a la pantalla 2 si no dona error   
    def log_in(self,uid):
  
        json_username = self.server_login(uid)
        username = json.loads(json_username)["name"]
        if username == 'ERROR':
            #self.error_uid()
            self.remove(self.pantalla1)
            self.create_pantalla1()
            thread_uid = threading.Thread(target=self.read_uid)  
            thread_uid.daemon = True
            thread_uid_in_use = False
            thread_uid.start()
            
        else:
            
            self.remove(self.pantalla1)
            self.create_pantalla2(username,uid)
            self.restart_timer()
            t.start()
            self.show_all()
            

    #Comunicació amb el servidor per fer login
    def server_login(self, uid):
        self.thread_login_in_use = True
        link = 'http://192.168.1.128/pbe/login.php?uid=' + uid
        with urllib.request.urlopen(link) as f:
            uname = f.read().decode('utf-8')
        self.thread_login_in_use = False
        return uname

    #Missatge d'error quan no es reconeix el uid
    def error_uid(self):   #misstge d'error
        print('Error uid')
        dialog = Gtk.MessageDialog(
            name = "error",
            transient_for=self,
            flags=0,
            message_type=Gtk.MessageType.ERROR,
            buttons=Gtk.ButtonsType.OK,
            text="Uid no reconegut",
        )
        dialog.format_secondary_text(
            "Si us plau torni a intentar-ho"
        )
        dialog.run()
        dialog.destroy()
        


#Interfície de la pantalla del course manager:
    
    #Es crea la pantalla amb el query     
    def create_pantalla2(self,user,uid):
        self.pantalla2 = Gtk.Grid(column_homogeneous=True,column_spacing=10,row_spacing=250)              
        self.add(self.pantalla2)

        usr = Gtk.Label()
        usr.set_text("Welcome " + user)
        self.pantalla2.attach(usr,0,0,5,1)


        logout = Gtk.Button(label="Logout")
        logout.set_hexpand(False)
        logout.connect("clicked", self.log_out_boton)
        self.pantalla2.attach(logout,5,0,1,1)

        entry = Gtk.Entry()
        entry.set_text("Insert query")
        entry.connect("activate", self.get_table_from_query,uid)
        self.pantalla2.attach(entry,0,1,6,1)

        user_lcd = user[:20]
        #display.lcd_clear()
        #display.lcd_display_string('Welcome', 2)
        #display.lcd_display_string(user_lcd, 3)
        
        

#Funcions associades a la pantalla del course manager:
        
    #Es passa el query al servidor i es mostren les dades
    def get_table_from_query(self,entry,uid):
        t.cancel()
        self.restart_timer()
        t.start()
        query = entry.get_text()
        neat_query = query.replace(' ','')
        json_timetable = self.server_send(neat_query,uid)
        print(json_timetable)
        timetable = json.loads(json_timetable)
        self.mostra_taula(timetable)

    #Mostra la taula a la pantalla
    def mostra_taula(self, taula):
        self.pantalla2.set_row_spacing(30)
        
        query = list(taula.keys())[1]
        
        #caso para 4 columnas
        if query == 'timetables':            
            self.table = Gtk.ListStore(str, str, str, str)       #creamos un modelo de TreeView a partir de una liststore de 4 columnas de strings
            
            columnas = list(taula[query][0].keys())       #hacemos una lista de los titulos/cabeceras de cada columna
            
            for row in taula[query]:
                self.table.append([row[columnas[0]],row[columnas[1]],row[columnas[2]],row[columnas[3]]])      #añadimos la información de cada fila
                
            self.treeview = Gtk.TreeView.new_with_model(self.table)       #creamos un TreeView a partir del modelo
                        
            for i, column_title in enumerate([columnas[0],columnas[1],columnas[2],columnas[3]]):    #loop para poner titulo y configurar el diseño de cada una de las columnas
                renderer = Gtk.CellRendererText()       #creamos un renderer
                renderer.set_fixed_size(220,40)       #definimos medidas de las celdas
                renderer.set_property("xalign",0.5)     #centramos los titulos
                renderer.set_property("background","#CED4DA")
                column = Gtk.TreeViewColumn(column_title,renderer,text=i)      #creamos un TreeViewColumn con las características anteriores que se añadirá al TreeView
                column.set_alignment(0.5)        #centramos los contenidos de las celdas
                self.treeview.append_column(column)       #añadimos el TreeViewColumn al TreeView
                 
            self.scrollable_treelist = Gtk.ScrolledWindow()          #hacemos que la ventana sea scrolled en cas que la tabla sea muy grande
            self.scrollable_treelist.set_vexpand(True)            #expandimos la tabla en la ventana
            self.pantalla2.attach(self.scrollable_treelist,0,3,6,6)       #añadimos la tabla en el grid
            self.scrollable_treelist.add(self.treeview)
            win.show_all()
            
        elif query == 'tasks' or query == 'marks':        #caso para 3 columnas
            self.table = Gtk.ListStore(str, str, str)       #creamos un modelo de TreeView a partir de una liststore de 3 columnas de strings
            
            columnas = list(taula[query][0].keys())      #hacemos una lista de los titulos/cabeceras de cada columna
            
            for row in taula[query]:         
                self.table.append([row[columnas[0]],row[columnas[1]],row[columnas[2]]])     #añadimos la información de cada fila
            
            self.treeview = Gtk.TreeView.new_with_model(self.table)            #creamos un TreeView a partir del modelo
            
            for i, column_title in enumerate([columnas[0],columnas[1],columnas[2]]):     #loop para poner titulo y configurar el diseño de cada una de las columnas
                renderer = Gtk.CellRendererText()         #creamos un renderer
                renderer.set_fixed_size(310,40)             #definimos medidas de las celdas
                renderer.set_property("xalign",0.5)       #centramos los titulos
                renderer.set_property("background","#CED4DA")
                column = Gtk.TreeViewColumn(column_title,renderer,text=i)         #creamos un TreeViewColumn con las características anteriores que se añadirá al TreeView
                column.set_alignment(0.5)           #centramos los contenidos de las celdas
                self.treeview.append_column(column)         #añadimos el TreeViewColumn al TreeView
                 
            self.scrollable_treelist = Gtk.ScrolledWindow()      #hacemos que la ventana sea scrolled en cas que la tabla sea muy grande
            self.scrollable_treelist.set_vexpand(True)           #expandimos la tabla en la ventana
            self.pantalla2.attach(self.scrollable_treelist,0,3,6,6)       #añadimos la tabla en el grid
            self.scrollable_treelist.add(self.treeview)
            win.show_all()
        
    
    #Tanca sessió i torna a la pantalla de login
    def log_out(self):
        t.cancel()#es posa 2 vegades perque amb 1 falla de vegades
        self.remove(self.pantalla2)
        self.create_pantalla1()
        self.show_all()
        t.cancel()
        thread_uid = threading.Thread(target=self.read_uid)  
        thread_uid.daemon = True
        thread_uid_in_use = False
        thread_uid.start()
        
    
    #Tanca la sessió desde el botó
    def log_out_boton(self, logout):
        self.log_out()

    
    #Comunicació amb el servidor per rebre dades
    def server_send(self, query, uid):
        if '?' not in query:
            link = 'http://192.168.1.128/pbe/index.php?' + query + '?uid='+uid
        else:
            link = 'http://192.168.1.128/pbe/index.php?' + query + '&uid='+uid
        self.thread_query_in_use = True
        with urllib.request.urlopen(link) as f:
            json_table = f.read().decode('utf-8')    
        self.thread_server_in_use = False
        return json_table

    


if __name__ == "__main__":
  win = Window()
  win.show_all()
  Gtk.main()
            
