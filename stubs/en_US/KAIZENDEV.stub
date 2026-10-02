# KaizenDev Modular API - Laravel Scaffold

![Laravel](https://img.shields.io/badge/Laravel-11%2B%20%7C%2012%2B%20%7C%2013%2B-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

Welcome to **KaizenDev Modular API**, an opinionated architectural scaffolding package for Laravel.
This package transforms the standard Laravel structure into a highly scalable, fully modular Monolith designed for Enterprise APIs.

---

## 🌟 O que é este pacote? (What is this package?)

Este pacote foi desenhado para resolver o problema de crescimento desordenado de APIs no Laravel. Em vez de ter dezenas de Controllers e Models misturados nas pastas raízes do framework, nós trazemos o conceito de **Monolito Modular** (Domain Driven Design).

Ao instalar o pacote, sua aplicação ganha um gerador inteligente (`make:module`) que cria módulos auto-contidos. Cada módulo possui sua própria:
- Controller
- Model
- Service (com Interface)
- Repository (com Interface)
- Requests (Store, Update, Index)
- Resources
- Factories, Seeders, Jobs, Exports e Migrations

Tudo isso sendo injetado dinamicamente no sistema sem que você precise registrar nenhuma classe manualmente!

---

## 🚀 Como Funciona a Mágica? (How it works)

O coração do pacote é o `ModuleServiceProvider`. Ele faz a leitura dinâmica da pasta `app/Http/Modules/` e registra automaticamente todos os *Bindings* de injeção de dependência (Interfaces de Repositórios e Serviços), além de carregar suas *Migrations* de forma isolada.

A comunicação da API é padronizada pelo `BaseTrait`, garantindo respostas JSON perfeitas e consistentes (sucesso, erro com validações) em toda a aplicação.

---

## 💻 Passo a Passo de Instalação (Installation Guide)

### 1. Requerendo o Pacote
No terminal da sua aplicação Laravel (recém-criada ou existente), execute:
```bash
composer require kaizendev/laravel-modular-api
```

### 2. Rodando o Wizard de Instalação
Inicie a configuração mágica rodando:
```bash
php artisan modular:install
```

Durante a instalação, o Wizard fará perguntas interativas (em Português ou Inglês, conforme sua escolha). Ele perguntará se você deseja excluir pastas padrões do Laravel (como `app/Models` e `app/Http/Controllers`) para garantir a pureza da arquitetura modular.

### 3. Criando seu primeiro Módulo!
Após a instalação, a fundação estará criada em `app/Http/Modules/Base`.
Agora, para gerar recursos reais de negócio, basta rodar:
```bash
php artisan make:module NomeDoModulo --table=nome_tabela
```
Pronto! Toda a estrutura será criada e já estará funcional no banco de dados.

---

## ❓ Perguntas Frequentes (FAQ)

**P: Eu preciso atualizar este pacote com frequência?**
*R: Não! O pacote lida com a geração estrutural de arquivos padrão do Laravel. Ele foi projetado para ser incrivelmente estável, exigindo atualização apenas em grandes transições de versões do PHP ou Laravel.*

**P: Como faço exceções de "fillables" no banco?**
*R: O `BaseModel` trabalha com "Autoload de Fillables" por padrão, protegendo as colunas vitais de ID e Data, liberando o resto. Caso precise alterar, use as funções auxiliares dinâmicas como `$this->addFillable()` ou `$this->addGuarded()` diretamente na sua Controller ou Service.*

---

## 👨‍💻 Sobre o Desenvolvedor

Esta arquitetura foi meticulosamente desenhada para escalar. Se você está usando este pacote, você está pronto para construir APIs que suportam milhares de requisições de forma limpa e manutenível.

*Desenvolvido com excelência por:*  
**Wanderson Borges** | [KaizenDev](https://kaizen.dev.br)

> "A melhoria contínua é melhor que a perfeição adiada." - *Kaizen*
