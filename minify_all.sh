shopt -s globstar
for p in ./**/*
do
  if [[ ${p##*.} == 'js' ]] || [[ ${p##*.} == 'css' ]]; then
    if [[ -f "./${p%.*}.min.${p##*.}" ]]; then
      echo "- Old minified file found..."
      rm "./${p%.*}.min.${p##*.}"
    fi
    if [[ ${p##*.} == 'js' ]]; then
      google-closure-compiler --js="./${p}" --js_output_file="./${p%.*}.min.${p##*.}"
    fi
    if [[ ${p##*.} == 'css' ]]; then
    	csso "./${p}" --output "./${p%.*}.min.${p##*.}"
    fi
  fi
done
