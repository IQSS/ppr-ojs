# Guides

As part of the documentation efforts we have built these guides that are published on [readthedocs.io](http://ppr-ojs.readthedocs.io).

## Building with Sphinx

### This is how to install sphinx: 

**On a Mac**: 

Download the sphinx zip file; I got mine from here: 
http://sphinx-doc.org/install.html

Unzip it somewhere. In the unzipped directory, do the following as
root, (sudo -i):

python setup.py build
python setup.py install

**Using pip (Mac/Unix/Windows)**:

* Unless you already have it, install pip (https://pip.pypa.io/en/latest/installing.html)
* run:

```
pip install sphinx
```

### How to build: 

```
make html
```

This will generate html files, and leave them in the build subdirectory here. 

If you want to clear your directory you can use:

```
make clean
```

## Building with docker

To build the container guides you can use the following command from the root of the project:

```
docker run -it --rm -v $(pwd):/docs sphinxdoc/sphinx:7.3.6 bash -c "cd docs/sphinx-guides && pip3 install -r requirements.txt && make html"
```

You can also use the following to make clean the build:
```
docker run -it --rm -v $(pwd):/docs sphinxdoc/sphinx:7.3.6 bash -c "cd docs/sphinx-guides && pip3 install -r requirements.txt && make clean"