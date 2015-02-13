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

Пароль до ел. пошти it.znu.edu@gmail.com дістається із файлу 
protected/gmail_password.txt
```
protected/
├── gmail_password.txt
```

У unix та linux-системах директорії assets, protected/runtime повинні бути доступні для запису
```
assets/
protected/
├── runtime/
```

В параметрі 'docPath' (main.php) вказується абсолютний шлях до місця збереження документів
```
protected/
├── config/
│   ├── main.php
```