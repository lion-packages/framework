# 🦁 Lion-Framework

<p align="center">
  <a href="https://lion-client.vercel.app/" target="_blank">
    <img width="1920" height="1080" alt="presentation-lion-packages" src="https://github.com/user-attachments/assets/2e6ced92-687d-49c7-bf0e-bad214b1fd5e" />
  </a>
</p>


<p align="center">
  <a href="https://packagist.org/packages/lion/framework">
    <img src="https://poser.pugx.org/lion/framework/v" alt="Latest Stable Version">
  </a>
  <a href="https://packagist.org/packages/lion/framework">
    <img src="https://poser.pugx.org/lion/framework/downloads" alt="Total Downloads">
  </a>
  <a href="https://github.com/lion-packages/framework/blob/main/LICENSE">
    <img src="https://poser.pugx.org/lion/framework/license" alt="License">
  </a>
  <a href="https://www.php.net/">
    <img src="https://poser.pugx.org/lion/framework/require/php" alt="PHP Version Require">
  </a>
</p>

🚀 **Lion-Framework** is a web application framework with a simple and powerful syntax, designed to help developers build fast and scalable applications.

---

## 📖 Features

✔️ Simple and fast routing engine.  
✔️ Dependency injection container.  
✔️ Native and easy-to-use database ORM.  
✔️ Database independent schema migrations.  

---

## 📦 Installation

Install the framework using **Composer**:

```bash
composer create-project lion/framework
```

## Usage Example

```php
use Lion\Route\Route;

Route::get('hello', function (): string {
    return "Hello, world! 🦁";
});
```

## 📝 License

The <strong>framework</strong> is open-sourced software licensed under the [MIT License](https://github.com/lion-packages/framework/blob/main/LICENSE).
