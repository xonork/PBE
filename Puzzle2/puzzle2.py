import gi
import threading
gi.require_version("Gtk", "3.0")
from gi.repository import Gtk
from gi.repository import Gdk 
from read import Rfid_PN532

class Window(Gtk.Window):
    def __init__(self):
        Gtk.Window.__init__(self, title="rfid_gtk.py")
        self.connect("destroy", Gtk.main_quit)
        self.set_border_width(10)
        
        
        
        self.box = Gtk.Box(orientation=Gtk.Orientation.VERTICAL, spacing=10)
        self.box.set_homogeneous(False)
        
        self.label = Gtk.Label(label="Please, login with your university card.")
        self.label.set_name("label")
        self.label.set_size_request(500,100)
        self.box.pack_start(self.label, True, True, 0)
        
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
        
        
        self.css_provider = Gtk.CssProvider()
        self.css_provider.load_from_data(self.blue)
        
        self.context = Gtk.StyleContext()
        self.screen = Gdk.Screen.get_default()
        self.context.add_provider_for_screen(self.screen, self.css_provider, Gtk.STYLE_PROVIDER_PRIORITY_APPLICATION)
        

        self.button = Gtk.Button(label="Clear")
        self.button.connect("clicked", self.on_button_clicked)
        self.box.pack_start(self.button, True, True, 0)

        

        self.add(self.box)

        self.thread = threading.Thread(target=self.write_uid)
        self.thread.daemon = True
        self.thread_in_use = True
        self.thread.start()

    def on_button_clicked(self, widget):
        
         if (self.thread_in_use == False):
            self.label.set_text("Please, login with your university card")
            self.css_provider.load_from_data(self.blue)
            self.thread = threading.Thread(target=self.write_uid)
            self.thread.start()
            self.thread_in_use = True
        

    def write_uid(self):
        self.rfid = Rfid_PN532()
        uid = self.rfid.read_uid()
        self.label.set_text("UID: "+uid)
        self.css_provider.load_from_data(self.red)
        self.thread_in_use = False


if __name__ == "__main__":
  win = Window()
  win.show_all()
  Gtk.main()

