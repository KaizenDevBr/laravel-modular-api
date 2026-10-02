# KaizenDev Modular API - Laravel Scaffold

<div align="center">
  <p>
    <a href="#-português-do-brasil">🇧🇷 Português do Brasil</a> &nbsp;&bull;&nbsp;
    <a href="#-english">🇺🇸 English</a>
  </p>
</div>

![Laravel](https://img.shields.io/badge/Laravel-11%2B%20%7C%2012%2B%20%7C%2013%2B-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

---

## 🇧🇷 Português do Brasil

Bem-vindo ao **KaizenDev Modular API**, um pacote de fundação arquitetural para Laravel.  
Este pacote transforma a estrutura padrão do Laravel em um **Monolito Modular** altamente escalável, projetado para APIs Enterprise.

### 🌟 O que é este pacote?
Este pacote resolve o problema de crescimento desordenado de APIs no Laravel. Em vez de centenas de Controllers e Models misturados, trazemos o conceito de *Domain Driven Design* (DDD) através de módulos.

A instalação fornece um gerador inteligente (`make:module`) que cria módulos auto-contidos com:
- Controller, Model, Repository, Service
- FormRequests, Resources, Migrations e Factories.

Tudo é injetado dinamicamente no ecossistema Laravel graças ao nosso `ModuleServiceProvider` customizado. Nenhuma classe precisa ser amarrada manualmente!

### 💻 Como Instalar

```bash
composer require kaizendev/laravel-modular-api
```
Em seguida, rode o instalador interativo (com suporte a PT-BR e EN-US):
```bash
php artisan modular:install
```
*Durante a instalação, o assistente perguntará se você deseja limpar as pastas padrões do Laravel (app/Models, app/Http/Controllers) para garantir a pureza da arquitetura.*

### 🚀 Criando um Módulo
```bash
php artisan make:module NomeDoModulo --table=nome_tabela
```
O pacote irá compor sua arquitetura completa em segundos!

<br>
<br>

---

## 🇺🇸 English

Welcome to **KaizenDev Modular API**, an opinionated architectural scaffolding package for Laravel.  
This package transforms the standard Laravel structure into a highly scalable, fully modular Monolith designed for Enterprise APIs.

### 🌟 What is this package?
This package solves the problem of messy API growth in Laravel. Instead of hundreds of Controllers and Models mixed in root folders, we bring the concept of *Domain Driven Design* (DDD) through modules.

Installation provides a smart generator (`make:module`) that creates self-contained modules featuring:
- Controller, Model, Repository, Service
- FormRequests, Resources, Migrations, and Factories.

Everything is dynamically injected into the Laravel ecosystem thanks to our custom `ModuleServiceProvider`. No classes need to be manually bound!

### 💻 How to Install

```bash
composer require kaizendev/laravel-modular-api
```
Next, run the interactive installer (supports both EN-US and PT-BR):
```bash
php artisan modular:install
```
*During installation, the wizard will ask if you want to clean up default Laravel folders (app/Models, app/Http/Controllers) to ensure architectural purity.*

### 🚀 Creating a Module
```bash
php artisan make:module ModuleName --table=table_name
```
The package will compose your complete architecture in seconds!

---

<div align="center">
  <p>
    <i>Desenvolvido com excelência por:</i><br>
    <b>Wanderson Borges | KaizenDev</b><br>
    <a href="https://kaizen.dev.br">https://kaizen.dev.br</a>
  </p>
  <p>
    <i>"A melhoria contínua é melhor que a perfeição adiada." - Kaizen</i>
  </p>
</div>
