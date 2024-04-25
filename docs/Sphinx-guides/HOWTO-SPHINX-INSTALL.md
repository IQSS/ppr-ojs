# This is how to install sphinx: 

## On a Mac: 

Download the sphinx zip file; I got mine from here: 
http://sphinx-doc.org/install.html

Unzip it somewhere. In the unzipped directory, do the following as
root, (sudo -i):

python setup.py build
python setup.py install

## Using pip (Mac/Unix/Windows):
* Unless you already have it, install pip (https://pip.pypa.io/en/latest/installing.html)

* run:

```
pip install sphinx
```

# How to build: 

```
make html
```

This will generate html files, and leave them in the build subdirectory here. 

If you want to clear your directory you can use:

```
make clean
```
