#!/usr/bin/env bash
à remplacer (<[^>]*)'
par $1_

à remplacer (<[^>]*)°
par $1o

à remplacer (<[^>]*)\(
par $1_
à lancer plusieurs fois

à remplacer (<[^>]*)\)
par $1_
à lancer plusieurs fois

xmllint --format --recover $1 > $2