
----------------------------------------------------------------------
----------------------------------------------------------------------
Server Database Extractor
----------------------------------------------------------------------

    Parses virtual host directories recursively looking for vhost configuration files.
    Extracts web directory information.
    Parses web directories recursively, extracts database credentials.
    Cycles through credential list and dumps data locally.
    Cycles through db dump files and inserts them into a remote server.


----------------------------------------------------------------------
    NOTA BENA Daemon:
----------------------------------------------------------------------

    Will probably use config.json file as source then delete the Description in config file!!


----------------------------------------------------------------------
    How To:
----------------------------------------------------------------------
    {
        "report": {
            "Description": "Report batch gruoping size. Do you want report summaries based upon per 100 / 1000 or 100000. ",
            "batch_size": 1000
        },
        "vhost": {
            "Description": "Nginx Virtual Hosts Base Directory path: Include a trailing forward slash. Include the suffix to search for passed as an array.",
            "dir_path": "/etc/apache2/sites-available/",
            "search_suffix": [
                "conf",
                "xxxxxx"
            ]
        },
        "web_config_file": {
            "Description": "Nginx Web Directory configuration file. Please provide full name of the file to search for. Can pass single value only.",
            "search_name": "configuration.php"
        },
        "log": {
            "Description": "Logging for application audit. This will log everything apart from reports and progress logs. Errors automatically log.",
            "enabled": false
        },
        "db": {
            "mysql_server_creds_source": {
                "Description": "Using Sitename here to make it easier to use with settings in web dir config file. MySQL ONLY! Root Database Login credentials. If you want to compare db's from vhosts to actual databases in the server.",
                "enabled": false,
                "sitename": null,
                "port": null,
                "username": null,
                "password": null
            },
            "mysql_server_creds_target": {
                "Description": "Using Sitename here to make it easier to use with settings in web dir config file. The target server where the db's will be inserted. Direct transport to Mysql Server.",
                "enabled": true,
                "sitename": "omnicasa-mysql-v2",
                "port": 3306,
                "username": "root",
                "password": "r7Xl5GRNhXVlMmcF"
            }
        }
    }

----------------------------------------------------------------------
----------------------------------------------------------------------
