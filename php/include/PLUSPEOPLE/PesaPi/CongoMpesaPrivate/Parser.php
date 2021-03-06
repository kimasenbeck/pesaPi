<?php
/*	Copyright (c) 2015, PLUSPEOPLE Kenya Limited. 
		All rights reserved.

		Redistribution and use in source and binary forms, with or without
		modification, are permitted provided that the following conditions
		are met:
		1. Redistributions of source code must retain the above copyright
		   notice, this list of conditions and the following disclaimer.
		2. Redistributions in binary form must reproduce the above copyright
		   notice, this list of conditions and the following disclaimer in the
		   documentation and/or other materials provided with the distribution.
		3. Neither the name of PLUSPEOPLE nor the names of its contributors 
		   may be used to endorse or promote products derived from this software 
		   without specific prior written permission.
		
		THIS SOFTWARE IS PROVIDED BY THE REGENTS AND CONTRIBUTORS ``AS IS'' AND
		ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
		IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
		ARE DISCLAIMED.  IN NO EVENT SHALL THE REGENTS OR CONTRIBUTORS BE LIABLE
		FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
		DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS
		OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
		HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
		LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
		OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF
		SUCH DAMAGE.

		File originally by Michael Pedersen <kaal@pluspeople.dk>
		Based on examples provided by Hassan Al Jirani
 */
namespace PLUSPEOPLE\PesaPi\CongoMpesaPrivate;

class Parser extends \PLUSPEOPLE\PesaPi\Base\Parser{
	const DATE_FORMAT = "j/n/Y h:i:s";

	public function parse($input) {
		$result = $this->getBlankStructure();

		// REFACTOR: should be split into subclasses
		// ARGENT RECU du 243881260264 le 13/01/2015 12:51:37 Du compte: 1000624832 Montant: 0.20 USD Frais: 0.00 USD Ref: 181346285 Solde Disponible: 0.20 USD
		if (strpos($input, " ARGENT RECU du ") !== FALSE) {
			$result["SUPER_TYPE"] = Transaction::MONEY_IN;
			$result["TYPE"] = Transaction::CD_MPESA_PRIVATE_PAYMENT_RECEIVED;

			$temp = array();
			preg_match_all("/ARGENT RECU du (\d+) le (\d\d?\/\d\d\/\d{4} \d\d?:\d\d:\d\d)[\s\n]+Du compte: (\d+)[\s\n]+Montant: ([0-9\.\,]+) USD[\s\n]+Frais: ([0-9\.\,]+) USD[\s\n]+Ref: (\d+)[\s\n]+Solde Disponible: ([0-9\.\,]+) USD/mi", $input, $temp);
			if (isset($temp[1][0])) {
				$result["RECEIPT"] = $temp[6][0];
				$result["AMOUNT"] = $this->numberInput($temp[4][0]);
				$result["PHONE"] = $temp[0][0];
				$result["TIME"] = $this->dateInput(Parser::DATE_FORMAT, $temp[2][0]);
				$result["BALANCE"] = $this->numberInput($temp[7][0]);
				$result["COST"] = $this->numberInput($temp[5][0]);				
			}

		} else {
			$result["SUPER_TYPE"] = Transaction::MONEY_NEUTRAL;
			$result["TYPE"] = Transaction::CD_MPESA_PRIVATE_UNKOWN;
		}

		return $result;
	}

}

?>