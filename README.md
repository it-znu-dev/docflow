## Система електронного документообігу та планування заходів ЗНУ

## Компоненти, які використано для функціонування ПЗ:

- [Yii framework 1.1.16](http://www.yiiframework.com/)
- [PEAR 1.9.5](http://pear.php.net/)
- [Bootstrap 3.3.1](http://getbootstrap.com/)
- [X-editable 1.5.1](http://vitalets.github.io/x-editable/)
- [bootstrap-datepicker 1.3.0](https://github.com/eternicode/bootstrap-datepicker)
- [jQuery File Upload 9.9.2](https://blueimp.github.io/jQuery-File-Upload/)
- [prettyPhoto 3.1.4](http://www.no-margin-for-errors.com/projects/prettyphoto-jquery-lightbox-clone/)

### Для встановлення ще потрібно

Активи css/js файлів, що створюються автоматично, надходять в директорію assets
```
assets/
```

Назва БД, користувач, пароль підключення до БД дістаються із файлу 
protected/config/db.txt
```
protected/
├── config/
│   ├── db.txt

```

Повний шлях до директорії збереження документів дістається із файлу 
protected/config/docPath.txt
```
protected/
├── config/
│   ├── docPath.txt
```

Електронна пошта і пароль для відправки повідомлень про розсилки респондентам дістаються із файлу
protected/config/email_config.txt
```
protected/
├── config/
│   ├── email_config.txt
```

Звіт про відправку інформації про розсилку по ел. пошті надходить до файлу
protected/logs/mail_logger.log
```
protected/
├── logs/
│   ├── mail_logger.log

```

Дані відповіді серевера сайту ЗНУ після відправки даних через CURL надходить до файлу
protected/logs/curl.log
```
protected/
├── logs/
│   ├── curl.log

```

У unix та linux-системах директорії 
  assets, protected/runtime, protected/logs 
повинні бути доступні для запису
```
assets/
protected/
├── runtime/
├── logs/
```
