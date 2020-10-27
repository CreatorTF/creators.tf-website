#!/usr/bin/env bash
shopt -s globstar

cd ./cdn/depot
for d in ./*/ ; do
cd "$d"
if [[ -f "./.ce-fastdl" ]]; then
git diff --name-only HEAD $1 > ./00
mkdir -p __bzip2

while read pattern; do
  # We want to rebuild the bz2 file if it's changed or it does not exist

  # First we check if file was changed. We read through the diff list and match the pattern.
  while read diff; do
    # if file exists - update bz2
    if [[ -f "../../../$diff" ]]; then
      if [[ `realpath --relative-to ./content ../../../$diff` == $pattern ]]; then
        if [[ ! -f "__bzip2/`realpath --relative-to ./content $diff`" ]]; then
          mkdir -p __bzip2/`realpath --relative-to ./content ${diff%/*}`
          cp $diff __bzip2/`realpath --relative-to ./content $diff`
        fi
      fi
    else
      # else we remove the bz2 because file was deleted
      if [[ -f "../../fastdl/`basename $d`/`realpath --relative-to ./content ../../../$diff`.bz2" ]]; then
        echo "Deleting `basename $diff`.bz2"
        rm ../../fastdl/`basename $d`/`realpath --relative-to ./content ../../../$diff`.bz2
      fi
    fi
  done < ./00

  # Next we go through all the files of this pattern and see if bz2 version exists.
  for i in ./content/$pattern; do
    [[ ! -f $i ]] && continue
    if [[ ! -f "../../fastdl/`basename $d`/`realpath --relative-to ./content $i`.bz2" ]]; then
      if [[ ! -f "__bzip2/`realpath --relative-to ./content $i`" ]]; then
        mkdir -p __bzip2/`realpath --relative-to ./content ${i%/*}`
        cp $i __bzip2/`realpath --relative-to ./content $i`
      fi
    fi
  done
done < .ce-fastdl
rm ./00

# bzip2 everything that needs to be changed
cd __bzip2
for i in ./**/*.*; do
  [[ ! -f $i ]] && continue
  echo "Archiving `basename $i`"
  bzip2 $i
done
cd ../

mkdir -p ../../fastdl/`basename $d`
cp -rl ./__bzip2/* ../../fastdl/`basename $d` 2>/dev/null || :
rm -rf ./__bzip2
fi
cd ../
done
