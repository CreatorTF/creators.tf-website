#!/usr/bin/env bash

shopt -s globstar

git submodule init
git submodule update --recursive

rm ./robots.txt

if [[ -f "./robots/$1.robots.txt" ]]; then
  cp ./robots/$1.robots.txt ./robots.txt
  echo "Found preset robots config for this branch. Using './robots/$1.robots.txt'."
else
  cp ./robots/@.txt ./robots.txt
  echo "Robots config not found. Using './robots/@.txt'."
fi

chmod 744 minify.sh
./minify.sh $2

# FastDL
exit 0

cd ./cdn/depot
for d in ./*/ ; do
  cd "$d"
  if [[ -f "./.ce-fastdl" ]]; then
    mkdir -p __bzip2
    while read pattern; do
      for i in ./content/$pattern; do
        mkdir -p __bzip2/`realpath --relative-to ./content ${i%/*}`
        cp $i __bzip2/`realpath --relative-to ./content $i`
      done
    done < .ce-fastdl

    cd __bzip2
    for i in ./**/*.*; do
      bzip2 $i
    done
    cd ../

    rm -rf ../../fastdl/$d
    cp -a ./__bzip2 ../../fastdl/$d

    rm -rf ./__bzip2
  fi
  cd ../
done
