-- Funkce ST_X() a ST_Y() ze sloupce protorového typu POINT (event.souradnice) získávají příslušnou souřadnici (Y - latitude; X - longitude)
-- Napište níže část SQL dotazu k seřazení záznamů podle souřadnice k získání jednoho _nejSEVERNĚJŠÍHO_ záznamu
-- Povinně uvádějte způsob řazení podle ASC nebo DESC.
-- příklad: ORDER BY ST_Y(souradnice) DESC LIMIT 0,1
ORDER BY ST_Y(souradnice) DESC LIMIT 0,1