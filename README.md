Rebuild of PhotoMem in Laravel.

images to by synced should be in public/storage/sync

After lando rebuild, need to manually instal python3-pip:
- lando ssh --user root
- apt-get update
- apt-get install python3-pip -y
- python3 -m pip install pillow
