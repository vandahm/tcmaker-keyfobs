# keyfobs

![Screenshot](/images/screenshot.png)

You definitely don't want to download this.

## Requirements

* PHP v7.0+
* CiviCRM 5.19 or greater

## Installation (Web UI)

You will never be able to install this from the web UI. Just deal with it.

## Installation (CLI, Zip)

[https://github.com/vandahm/tcmaker-keyfobs/releases/](https://github.com/vandahm/tcmaker-keyfobs/releases/)

To make your own ZIP file:

```bash
git clone https://github.com/vandahm/tcmaker-keyfobs.git
cd tcmaker-keyfobs
composer Install
zip -r keyfobs.zip . -x '.git/*'
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/vandahm/tcmaker-keyfobs.git
cd tcmaker-keyfobs
composer install
cv en keyfobs
```

## Usage

Be sure to set your AWS credentials in System Settings.

## Known Issues

There's all kinds of stuff wrong with this extension.
