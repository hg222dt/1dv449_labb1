1dv449_labb1
============
#Vad tror du vi har för skäl att spara det skrapade datat i json?
Json är såvitt jag vet ett välanvänt och beprövat format som används i många sammanhang och är kompatibelt i många situationer. I php och javascript finns det dessutom färdiga encodare/decodare som gör json-skrivningen/läsningen väldigt flexibel och enkel. Json har dessutom en struktur som är relativt lättläst genom att bara titta på den.

#Olika jämförelsesiter är flitiga användare av webbskrapor. Kan du komma på fler typer av tillämplingar där webbskrapor förekommer
Sökmotorer använder sig av samma logik, kanske med undantaget att vi i denna övning har skrapat relativt begränsade delar av ett par webb-sidor. Det finns andra typer av tjänster som skrapar av sociala medier, för att kunna läsa av olika typer av trender och attitydet, vilket är värdefull information för väldigt många företag. Till sist kan även nämnas nyhets-retrrievers som scannar olika nyhets-siter/bloggar på nyheter som skrivs, för att samla i register eller förmedla utsorterat material till olika intressenter.

#Hur har du i din skrapning underlättat för serverägaren?
Jag har använt mig av en cashnings-strategi som gjort att servern inte belastas av min skrapa, mer än en gång vart femte minut, vilket med ett antal användaren tydligt skulle minska belastningen på servern.
Sen har jag även tydligt identifierat mig genom mitt cURL-anrop, vilket också får ses som ett underlättande för att serverägaren ska kunna se vem som gör skrapningen.

#Vilka etiska aspekter bör man fundera kring vid webbskrapning?
Materialet som man skrapar skulle kunna vara skyddat av copyright, och ägaren kanske inte är så intresserad av att detta ska skrapas, eller för den delen användas på annat sätt efter skrapaningen, exempelvis i kommersiella syften. En site har ibland ett “Terms of use”-avsnitt som förtydligar detta.

#Vad finns det för risker med applikationer som innefattar automatisk skrapning av webbsidor? Nämn minst ett par stycken!
Delvis kan en server bli överbelastad om för många anrop görs mot den. Detta kan i synnerhet ske när man skrapar många sidor på en site på en gång, och om många webbskrapor gör detta, multipliceras ju denna belastning. Information kan även av webbplatsägaren ha lagts in på annat sätt än i enkelt läsbar html-kod, dvs i evempelis bilder mm. Detta gör att skrapan inte uppfattar allt. Automatiken i skrapandet kan även skapa komplikationer. Detta då exempelvis en sites struktur kan förändras, och skrapandet förvillas till att hämta ut och identifiera fel webbside-komonenter på fel plats.

#Tänk dig att du skulle skrapa en sida gjord i ASP.NET WebForms. Vad för extra problem skulle man kunna få då?
Det blir problem med ViewState som WebForms använder sig av, då denna från servern alltid skickar med extrainformation för att spara vilket state en användare befinner sig i.

#Välj ut två punkter kring din kod du tycker är värd att diskutera vid redovisningen. Det kan röra val du gjort, tekniska lösningar eller lösningar du inte är riktigt nöjd med.
Jag skulle nog främst vilja diskutera hur man skulle skrapa resultat av ajax-anrop, eftersoma tt mitt sätt att skrapa pagineringen, utgår ifrån att skrapa den url som jag kan nå de olika paginerade sidorna via. Men jag tänker mig hur scenariot skulle se ut om det inte skulle finnas samma möjlighet till att nå sidorna genom unika url:er.
Jag har valt att göra mitt extraherande av data med hjälp av xPath från sidorna som skrapats, samt att sätta in detta i enskilda objekt för varje varje skrapad sida. Jag funderar på om detta inte skulle kunna göras på ett mer effektivt sätt, genom att i en slags lista samla alla xPath-uttryck, som sedan itereras igenom tillsammans med metodanrop för att extrahera data. 

#Hitta ett rättsfall som handlar om webbskrapning. Redogör kort för detta.
Facebook vann 2009 ett rättsfall mot siten Power.com, som försökte skapa ett gränssnitt för användare att samla olika sociala medier på ett ställe. Facebook hävdade att man brutit mot deras Terms of use. Det intressanta var att rätten ansåg att Power hade gjort copyright-intrång, detta trots att det användargenererade materialet på Facebook inte enligt rätten ansågs tillhöra Facebook, utan användarna själva.Facebook hävdade dock att en skrapning inte kan genomföras utan att skrapan inte skrapar och gör intrång på deras eget material som finns runt det användargenererade materialet på Facebook. Dvs Power hade skrapat Facebooks egna copyright-skyddade material trots att de i slutändan bara ville komma åt en användares egna data. Domstolen gick på Facebooks linje och tilldelade dem seger i fallet.

#Känner du att du lärt dig något av denna uppgift?
Absoult. För mig var det intressant att få arbeta med xPATH, något jag känner att jag måste utforska mer. Det skulle vara intressant att djupare utforska hur man kan analysera webbsidestrukturer för att automatiskt analysera en webbsidestruktur, och autmatiskt extrahera och klassificera deta och dess relevans. Sen har jag inte handgripligen arbetat så mycket json innan heller, vilket var bra och nyttigt för mig.
