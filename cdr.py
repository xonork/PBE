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

        
        

        

        '''
        #define blue style
        self.blue = b""" 
                    box {
                        margin: 0px;
                    }
                    
                    button{
                        background-color: #E0D4D4;
                        box-shadow:#00000 5px 5px 1px;
                        margin: 20px 10px 10px 10px;
                    }
                
                    #label{
                      background-color: #3393FF;
                      font: bold 24px Verdana;
                      border-radius:20px;
                      color:#FFFFFF;
                      padding: 50px;
                      margin: 20px;
                    }


                """
        
        #define red style    
        self.red = b"""
                    box {
                        margin: 50px;
                    }
                    
                    button{
                        background-color: #E0D4D4;
                        box-shadow:#00000 5px 5px 1px;
                        margin: 20px 10px 10px 10px;
                        }
                    #label{
                      background-color: #FA0000;
                      font: bold 24px Verdana;
                      border-radius:20px;
                      color:#FFFFFF;
                      margin: 10px 20px 20px 20px;
                    }
                    
                """
    
        self.css_provider = Gtk.CssProvider() #Adding styles
        self.css_provider.load_from_data(self.blue) 
        self.context = Gtk.StyleContext()
        self.screen = Gdk.Screen.get_default()
        self.context.add_provider_for_screen(self.screen, self.css_provider, Gtk.STYLE_PROVIDER_PRIORITY_APPLICATION)
'''
        '''#Thread per el lector de targetes
        self.thread_uid = threading.Thread(target=self.log_in)  
        self.thread_uid.daemon = True
        self.thread_uid_in_use = True
        self.thread_uid.start()'''

        
    """def on_button_clicked(self, widget):  #function when "clear" button is clicked
         if (self.thread_uid_in_use == False):
            
            self.label.set_text("Please, login with your university card")
            self.css_provider.load_from_data(self.blue)
            self.thread_uid = threading.Thread(target=self.uid)
            self.thread_uid.start()
            self.thread_uid_in_use = True"""
    
        
    	
        
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
        print('Pantalla')
        #self.log_in()
        #threading.Thread(target=self.log_in, daemon=True)
        
        
    

#Funcions associades a la pantalla de log in:

    def read_uid(self):
        #rf = RFID()
        #uid = Rfid.read_uid(self.rf)
        self.uid = "890C769C"
        threading.Thread(target=self.log_in, args=(self.uid,), daemon = True).start()
        
    #Funcio login
    #Si reconeix el uid passa a la pantalla 2 si no dona error   
    def log_in(self,uid):
  
        json_username = self.server_login(uid)
        username = json.loads(json_username)["name"]
        if username == 'ERROR':
            self.error_uid()
            self.remove(self.pantalla1)
            self.create_pantalla1()
        else:
            
            self.remove(self.pantalla1)
            self.create_pantalla2(username,uid)
            print("flag4")
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
            

