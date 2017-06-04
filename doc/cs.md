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
- synchronizace identity - do session se ukládá pouze id a ostatní data do jiné storage
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
```

## Authenticator
K dispozici je [AuthenticatorFacadeAbstract](../src/Security/AuthenticatorFacadeAbstract.php) bude stačit když jej podědíte a doplníte metody které nejsou naimplementované z rozhraní [AuthenticatorFacadeInterface](../src/Security/AuthenticatorFacadeInterface.php). Můžeme se nechat inspirovat [UserModel](../tests/libs/UserModel.php) třídou pro testování. Novou třídu registrujde jako službu v neonu.

## Authorizator
Implementujte rozhraní [PermissionInterface](../src/Security/PermissionInterface.php), kde si určíte která id uživatelů jsou implicitně povolená na všechno pokud nechcete stačí vracet FALSE.

V aplikaci používáte ověřování pomocí resource a privilege a můžete si doplnit parametry například id souboru a ověřit si zda uživatel na něj má právo.

```php
$user->isAllowed('file', 'list-view');
$user->isAllowed('file', 'read', 1);
$user->isAllowed('file', 'upload');
$user->isAllowed('file', 'delete', 2, 'foo', 'bar');
```

Vždy když si takto něco napíšete aplikace vyhodí vyjímku a řekně co máte doplnit do třídy za metodu. Pokud potřebujete vlastní třídu User, tak poděďte tu co je zde v rozšíření.

## UserStorage
Přijímá dvě uložiště, jedno nativní z nette, a druhé jde ovlivnit přes tento doplněk a do tohoto se právě ukládají všechna data, tím odpadnou problémy s přihlášením na pc, mobilu, tabletu, notebooku a jinde, uživatel si něco změní v mobilu a hned se to projeví v ostatních zařízeních.

## SynchronizeIdentity
Tato třída řeší odhlášení všech popřípadě jednotlivců. Pokud na webu někomu přidáte/odeberete práva, stačí zavolat metodu forceReloadUserIdentity($userId) a dojde k obnovení identity, danné osoby. To samé v případě udělění banu.
