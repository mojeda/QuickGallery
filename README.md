QuickGallery
============

Quick Gallery allows you to upload image folders to make an online gallery with support for multiple image galleries.

Supports by default: png, jpg, jpeg, and gif but you are able to extend this by modifying line 53:

```$allowed_types = array('png','jpg','jpeg','gif');```

Screenshot
============
![Quick Gallery](http://i.imgur.com/VqTPcAw.png)

Instructions
============
1. Download index.php and thumb.php
2. Upload to a folder accessible by the internet (e.g. A folder called "gallery")
3. Upload image albums to their own folder.
4. Profit???

To enable caching, create a folder in the root of your gallery folder called "cache" and it give it 777 permissions.

File Tree Example
============
gallery  
├── cache  
├── index.php  
├── simcity  
│   ├── Spark_2013-03-18_15-12-33.png  
│   ├── Spark_2013-03-18_15-25-06.png  
│   ├── Spark_2013-03-18_15-25-18.png  
│   ├── Spark_2013-03-18_15-25-35.png  
│   ├── Spark_2013-03-18_15-25-50.png  
├── team fortress 2  
│   ├── 2011-02-24_00001.jpg  
│   ├── 2011-05-09_00001.jpg  
│   ├── 2011-05-09_00002.jpg  
├── thumb.php  

Requirements
============
* PHP + PHP GD
* Web Server
