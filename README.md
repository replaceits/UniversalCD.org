# UniversalCD.org

Universal Community Developers is a community based non-profit organization. We are an alliance of concerned citizens geared towards giving back, paying it forward and creating change in one youth, one family and one community at a time, while fostering a universal development process and creating safe environments for all.

##Database
```
+-----------------------+
| Tables_in_universalcd |
+-----------------------+
| mailing_list          |
+-----------------------+
```

```
+----------------------------------------------------------------------+
| DESCRIBE mailing_list                                                |
+---------------+--------------+------+-----+---------+----------------+
| Field         | Type         | Null | Key | Default | Extra          |
+---------------+--------------+------+-----+---------+----------------+
| email_id      | int(5)       | NO   | PRI | NULL    | auto_increment |
| email_address | varchar(255) | NO   | UNI | NULL    |                |
| join_date     | date         | NO   |     | NULL    |                |
+---------------+--------------+------+-----+---------+----------------+
```
