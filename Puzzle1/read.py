from py532lib.i2c import *
from py532lib.frame import *
from py532lib.constants import *


class Rfid_PN532:
	def read_uid(self):
		pn532 = Pn532_i2c()
		pn532.SAMconfigure

		card_data = pn532.read_mifare().get_data() #Aquest mètode retorna en binari
		hex_data = card_data.hex() #El que faig aquí és passar-ho a un string hexadecimal
		hex_length = len(hex_data) 
		uid = hex_data[(hex_length-8):(hex_length)].upper() #Em vaig donar compte que em retornava més valors dels que hauria, amb el nfc-poll vaig apuntar el uid
		return  uid						#d'una de les targes, i vaig veure si aquesta seqüència es repetia en alguna part del que em retornava
									#el mètode read_mifare().get_data(), i efectivament, la seqüència estava al final del string 


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
	



	


