import gi
import threading
gi.require_version("Gtk", "3.0")
from gi.repository import Gtk
from gi.repository import Gdk 
from read import Rfid_PN532

class Window(Gtk.Window):
    def __init__(self):
        
        #CREEM LA FINESTRA
        Gtk.Window.__init__(self, title="rfid_gtk.py")
        self.set_position(Gtk.WindowPosition.CENTER)
        self.connect("destroy", Gtk.main_quit)
        self.set_border_width(10)
        
        
        #CREEM UNA CAIXA
        self.box = Gtk.Box(orientation=Gtk.Orientation.VERTICAL, spacing=10)
        self.box.set_homogeneous(False)
        
        #CREEM UNA ETIQUETA QUE ANIRA DINS LA CAIXA
        self.label = Gtk.Label(label="Please, login with your university card.")
        self.label.set_name("label")
        self.label.set_size_request(500,100)
        self.box.pack_start(self.label, True, True, 0)
        
        #CREEM ELS ELSTILS EN CSS QUE UTILITZAREM EN EL PROGRAMA
        self.blue = b"""
                
                button{
                    background-color: #E0D4D4;
                    box-shadow:#00000 5px 5px 1px;
                    }
            
                #label{
                  background-color: #3393FF;
                  font: bold 26px Verdana;
                  border-radius:20px;
                  color:#FFFFFF;
                }
                
            """
        
        self.red = b"""
                
                button{
                    background-color: #E0D4D4;
                    box-shadow:#00000 5px 5px 1px;
                    }

                #label{
                  background-color: #FA0000;
                  font: bold 26px Verdana;
                  border-radius:20px;
                  color:#FFFFFF;
                }
                
            """
        
        #CARGUEM ELS NOSTRES ESTILS CSS
        self.css_provider = Gtk.CssProvider()
        self.css_provider.load_from_data(self.blue)
        
        self.context = Gtk.StyleContext()
        self.screen = Gdk.Screen.get_default()
        self.context.add_provider_for_screen(self.screen, self.css_provider, Gtk.STYLE_PROVIDER_PRIORITY_APPLICATION)
        
        #CREEM UN BUTÓ I EL FIQUEM DINS LA CAIXA
        self.button = Gtk.Button(label="Clear")
        self.button.connect("clicked", self.on_button_clicked)
        self.box.pack_start(self.button, True, True, 0)

        
        #FIQUEM LA NOSTRE CAIXA A LA FINESTRA
        self.add(self.box)

        #INICIALITZEM EL NOSTRE THREAD, EL QUAL S'ENCARREGA DE LLEGIR EL UID DE LA NOSTRE TARGETA
        self.thread = threading.Thread(target=self.write_uid)
        self.thread.daemon = True
        self.thread_in_use = True
        self.thread.start()

    #FUNCIÓ QUE ES CRIDADA QUAN PULSEM, LA QUAL FA QUE LA ETIQUETA TORNI AL SEU ESTAT INICIAL
    def on_button_clicked(self, widget):
        
         #AQUESTA FUNCIÓ NOMÉS ES POT UTILITZAR SI L'ETIQUETA ESTÀ EN L'ESTAT ON IMPRIMEIX EL UID
         if (self.thread_in_use == False):
            
            #ES MODIFICA L'ETIQUETA, SE LI TORNA A POSAR EL SEU ESTIL CSS INICIAL I ES TORNA A INICIALITZAR EL THREAD
            self.label.set_text("Please, login with your university card")
            self.css_provider.load_from_data(self.blue)
            self.thread = threading.Thread(target=self.write_uid)
            self.thread.start()
            self.thread_in_use = True
        
    #FUNCIÓ QUE S'UTILITZA COM A THREAD
    def write_uid(self):
        
        #ES CREA UN OBJECTE DE CLASE RFID_PN532 PER PODER LLEGIR EL UID.
        #UNA VEGADA LLEGIT, S'IMORIMEIX A L'ETIQUETA I A LA VEGADA A AQUESTA SE LI CANVIA EL ESTIL CSS
        self.rfid = Rfid_PN532()
        uid = self.rfid.read_uid()
        self.label.set_text("UID: "+uid)
        self.css_provider.load_from_data(self.red)
        self.thread_in_use = False


if __name__ == "__main__":
  win = Window()
  win.show_all()
  Gtk.main()

