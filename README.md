# Implementazione generica di Sender per l'invio di Fatture Elettroniche ad intermediari per lo SDI

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fatturaelettronicaphp/sender.svg?style=flat-square)](https://packagist.org/packages/fatturaelettronicaphp/sender)
[![Tests](https://github.com/fatturaelettronicaphp/sender/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/fatturaelettronicaphp/sender/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/fatturaelettronicaphp/sender.svg?style=flat-square)](https://packagist.org/packages/fatturaelettronicaphp/sender)

Implementazione generica di Sender per l'invio di Fatture Elettroniche ad intermediari per lo SDI

## Installazione e Utilizzo

E' suggerito (ma non obbligatorio) l'utilizzo della [libreria principale](https://github.com/fatturaelettronicaphp/FatturaElettronica) per la lettura, scrittura e validazione dei file xml.

Questo pacchetto **funziona anche indipendentemente** utilizzando la stringa XML del file di fatturazione elettronica.

L'utilizzo ricalca quello di [Flysystem](https://github.com/thephpleague/flysystem), popolare libreria di gestione file in PHP.

Sono disponibili vari "adapter" per i vari provider di Hub SDI, ed è possibile scrivere i propri adapter nel caso non siano disponibile nella libreria. Sono benvenute PR per l'aggiunta di nuovi adapter.

La procedura consiste nel scegliere un adapter e installarlo con composer. Nell'esempio si vede come fare per Aruba

```composer require fatturaelettronicaphp/sender fatturaelettronicaphp/sender-aruba```

E' poi necessario instanziare l'adapter e utilizzarlo tramite il Sender principale:

```php
$adapter = new \FatturaElettronicaPhp\Sender\Adapter\Aruba\ArubaAdapter([
    'username' => '[USERNAME]',
    'password' => '[PASSWORD]',
    'environment' => \FatturaElettronicaPhp\Sender\Adapter\Aruba\ArubaAdapter::ENV_PRODUCTION,
]) 
$sender = new \FatturaElettronicaPhp\Sender\Sender($adapter);
$sender->send('[XML]');
```

Di default il pacchetto cerca in automatico una implementazione PSR-18 di un client HTTP per inviare le richieste ai sender, per cui se il progetto nel quale questa libreria viene inserita ha già a disposizione un client http, il sistema lo rileva in automatico e lo utilizza di default.

E' comunque possibile installare un qualunque client http compatibile e fornirlo alla librerie Tramite la funzione `withClient`.

```php
$client = new GuzzleHttp\Client;
$sender = new \FatturaElettronicaPhp\Sender\Sender($adapter);
$sender->withClient($client);
```

## Scrivere un Adapter

E' possibile scrivere un nuovo adapter.
Tale adapter deve solo implementare l'interfaccia `FatturaElettronicaPhp\Sender\Contracts\SenderAdapterInterface`.

Per un più veloce sviluppo, si consiglia di estendere la classe `FatturaElettronicaPhp\Sender\Adapter\AbstractAdapter` che fornisce le basi per la gestione tramite richieste HTTP.

## Testing

Per eseguire alcuni test, è necessario avere le credenziali dei vari adapter. Di default la suite di test marca come `skipped` i test per cui sono necessarie tali credenziali, e non sono state fornite.

Per fornire tali credenziali, copiare il file `.auth.json.dist` in `.auth.json` e popolare le chiavi necessarie.

Per lanciare la suite di test:

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Daniele Rosario](https://github.com/Skullbock)
- [Kristian Lentino](https://github.com/KristianLentino99),
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
