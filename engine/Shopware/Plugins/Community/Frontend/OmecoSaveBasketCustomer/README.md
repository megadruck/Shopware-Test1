omeco(R) Warenkorbgültigkeit für eingeloggte Kunden
==================================================

Beschreibung
------------
Dieses Plugin ermöglicht, dass der Warenkorb für Kunden eine Gültigkeit von N Tagen hat.
Sobald der Kunde im Shop durch sein Login identifiziert wurde, stellt ihm der Shop 
seinen Warenkorb mit dem addierten Inhalt zu Verfügung.

Die Tage, die der Warenkorb gültig ist, wird in der Plugin Konfiguration als Variable 
erstellt. Nach Ablauf der 30 Tage wird der Warenkorb gelöscht.

DatenBank
----------
s_order_basket_attributes
* om_user_id
* om_date_update

Name Plugin
------------
OmecoSaveBasketCustomer

Verzeichnisstruktur
-------------------
* OmecoSaveBasketCustomer
** Components
*** OrderBasket.php
** Bootstrap.php
** CHANGELOG.md
** LICENSE.md
** README.md
** INSTALL.md
** plugin.png