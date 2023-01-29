
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
        "dir_path": "/home/mila/Repos/dbExtractor/testdir/etc/nginx/",
        "search_suffix": [
            "conf"
        ]
    },
    "log": {
        "Description": "Logging for application audit. This will log everything apart from reports and progress logs. Errors automatically log.",
        "enabled": true
    },
    "root_db_login": {
        "Description": "MySQL ONLY! Root Database Login credentials. If you want to compare db's from vhosts to actual databases in the server.",
        "enabled": false,
        "config_name": "db_root_config.json"
    },
    "target_server": {
        "Description": "The target server where the db's will be inserted. Direct transport to Mysql Server.",
        "ip_address": null,
        "port": null,
        "username": null,
        "password": null
    }
}


----------------------------------------------------------------------
----------------------------------------------------------------------
