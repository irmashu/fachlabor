SELECT auftrag.Reihenfolge, auftrag.AuftragsNr, fertigung.Stadt AS Fertigungsstandort, sku.Name, auftrag.SKUNr, sind_in.Bestand, auftrag.Status, SUM(gehoert_zu.Quantitaet) AS Losgroesse, servicepartner.VIPKunde
FROM auftrag 
LEFT JOIN sku ON auftrag.SKUNr = sku.SKUNr 
LEFT JOIN fertigung ON auftrag.FertigungsNr = fertigung.FertigungsNr 
LEFT JOIN sind_in ON auftrag.SKUNr = sind_in.SKUNr 
LEFT JOIN gehoert_zu ON auftrag.AuftragsNr = gehoert_zu.AuftragsNr 
LEFT JOIN bestellung ON gehoert_zu.BestellNr = bestellung.BestellNr 
LEFT JOIN servicepartner ON bestellung.ServicepartnerNr = servicepartner.ServicepartnerNr 
WHERE fertigung.FertigungsNr = 1 AND auftrag.Status != "Fertig" 
GROUP BY auftrag.AuftragsNr ORDER BY auftrag.Reihenfolge ASC; 

SQL-Query 1:
SELECT Foto, Name, sku.SKUNr, Beschreibung, Preis, sind_in.Bestand
FROM airlimited.sku
LEFT JOIN sind_in ON sku.SKUNr = sind_in.SKUNr 
WHERE Preis > 0 AND Preis < 1000 
ORDER BY SKUNr ASC LIMIT 1000;
