-- Vymyslete a vepiště níže vlastní SQL dotaz, který bude zajímavým způsobem 
-- (!!!dotazy 09, 10, 11 budou významově originální, ne jen vzájemné doplňky!!!)
-- pomocí prostorových fukncí filtrovat body nálezů vůči polygonům jezer v tabulce jezera. 
-- Nápověda: další tabulku (např. jezera) nemusíte nutně JOINovat, stačí ji přidat s čárkou: ... FROM tabulka1, tabulka2 ...
-- V SELECTu musí být povinně použity tyto položky:
-- gbifID, scientificName, ST_AsText(souradnice) AS souradniceWKT, souradnice, geo_poly
-- Zdůvodněte zde do poznámky, co je cílem Vámi navrhnutého SQL dotazu.
