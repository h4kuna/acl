Authenticator
=============

Knihovna je napsaná pro [Nette framework](www.nette.org) a PHP 5.4+.

K instalaci použijte composer:
```sh
composer require h4kuna/acl
```

S čím knihovna pomůže
------------------
- authenticator - připraveny jsou metody pro příhlášení pomocí id a pomocí hesla
- authorizator - stačí naimplementovat PermissionInterface a zaregistrovat jako službu
- global identity - do session se ukládá pouze id a ostatní data do jiné storage
- umožňuje cílené obnovení identit všem uživatelům (vyprázdněním storage), nebo jen jednotlivcům

# Jak integrovat

Zaregistrujeme rošíření v neonu.
```sh
extensions:
	aclExtension:
		h4kuna\Acl\DI\AclExtension

aclExtension:
	# volitelné
	storage: @mojeStorage # default @cache.storage
	debugMode: %debugMode%
```

## Authenticator
K dispozici je [AuthenticatorFacade](../src/Security/AuthenticatorFacade.php) bude stačit když jej implementujete. Můžeme se nechat inspirovat [UserModel](../tests/libs/UserModel.php) třídou pro testování. Novou třídu registrujde jako službu v neonu.

## Authorizator
Implementujte rozhraní [Permission](../src/Security/Permission.php) methoda **isGod()**, kde si určíte která id uživatelů jsou implicitně povolená na všechno pokud nechcete stačí vracet FALSE.

V aplikaci používáte ověřování pomocí resource a privilege a můžete si doplnit parametry například id souboru a ověřit si zda uživatel na něj má právo.

```php
$user->isAllowed('file', 'list-view');
$user->isAllowed('file', 'read', 1);
$user->isAllowed('file', 'upload');
$user->isAllowed('file', 'delete', 2, 'foo', 'bar');
```

Vždy když si takto něco napíšete aplikace vyhodí vyjímku a řekně co máte doplnit do třídy za metodu. Pokud potřebujete vlastní třídu User, tak poděďte tu co je zde v rozšíření. V debug baru se objeví nová ikonka, která vám napoví jaké jsou možnosti jsou autorizace.

## GlobalIdentity
Globální uložiště nezávislé na session a servery.

## TODO
- sami si ověřte zda uživatel není blokovaný
