# Plan Jazd – API w czystym PHP

Prosty system zarządzania jazdami instruktora.  
Backend napisany w czystym PHP z użyciem PDO i MySQL.

## Wymagania
- PHP 8+
- MySQL
- Apache/Nginx

## Instalacja
1. Zaimportuj `schema.sql` do bazy danych.
2. Ustaw dane połączenia w `config.php` lub `.env`.
3. Uruchom `index.php` w przeglądarce.

## Endpointy API
- `GET api.php?action=get` — pobiera wszystkie jazdy  
- `POST api.php?action=add` — dodaje nową jazdę (JSON body)  
- `DELETE api.php?action=delete&id={id}` — usuwa jazdę po ID
