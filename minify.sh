#!/usr/bin/env bash

shopt -s globstar

# First we get the difference between commits
git diff --name-only HEAD $1 | grep .js$ > ./00
git diff --name-only HEAD $1 | grep .css$ > ./01

echo "[PENDING!] Starting processing of JS files."
while read p; do
  echo "JS has changed: $p. Reminifying..."
  echo "- Searcing for ./${p%.*}.min.js"
  if [[ -f "./${p%.*}.min.js" ]]; then
    echo "- Old minified JS file has been found. Removing..."
    rm "./${p%.*}.min.js"
  fi
  if [[ -f "./${p%.*}.js" ]]; then
    google-closure-compiler --js="./${p}" --js_output_file="./${p%.*}.min.${p##*.}"
  	echo "- Reminified at: ./${p%.*}.min.${p##*.}"
  fi
done < ./00
rm ./00

echo "[SUCCESS!] JS files are minified."

echo "[PENDING!] Starting processing of CSS files."
while read p; do
  echo "CSS has changed: $p. Reminifying..."
  echo "- Searcing for ./${p%.*}.min.css"
  if [[ -f "./${p%.*}.min.css" ]]; then
    echo "- Old minified CSS file has been found. Removing..."
    rm "./${p%.*}.min.css"
  fi
  if [[ -f "./${p%.*}.css" ]]; then
  	csso "./${p}" --output "./${p%.*}.min.${p##*.}"
  	echo "- Reminified at: ./${p%.*}.min.${p##*.}"
  fi
done < ./01
rm ./01

echo "[SUCCESS!] CSS files are minified."
