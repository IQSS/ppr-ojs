# Build Sphinx Guides with docker

To build the guide, use the Make target ```guides```
```
make guides
```

To build the guides you can use the following command from the root of the project:
```
docker run -it --rm -v $(pwd):/docs sphinxdoc/sphinx:7.3.6 bash -c "cd docs/sphinx-guides && pip3 install -r requirements.txt && make html"
```

You can also use the following to make clean the build:
```
docker run -it --rm -v $(pwd):/docs sphinxdoc/sphinx:7.3.6 bash -c "cd docs/sphinx-guides && pip3 install -r requirements.txt && make clean"
```