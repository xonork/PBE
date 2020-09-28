from py532lib.i2c import *
from py532lib.frame import *
from py532lib.constants import *


class Rfid_PN532:
	def read_uid(self):
		pn532 = Pn532_i2c()
		pn532.SAMconfigure

		card_data = pn532.read_mifare().get_data()
		hex_data = card_data.hex()
		hex_length = len(hex_data)
		uid = hex_data[(hex_length-8):(hex_length)].upper()
		return  uid



if __name__ == "__main__":

	again = True

	while again == True:

		print(">>>>>>PASE SU TARJETA POR EL ESCANER<<<<<<<\n")

		rf = Rfid_PN532()
		uid = rf.read_uid()
		print("SU UID ES: " + uid + "\n")

		correct = False

		while correct == False:

			print("¿QUIERE VOLVER A PASAR LA TARJETA?\n Y, PARA SEGUIR\n N, PARA SALIR\n")
			response = str(input())
			if (response == 'Y' or response == 'y'):
				again = True
				correct = True

			elif (response == 'N' or response == 'n'):
				again = False
				correct = True

			else:
				correct = False
	



	


