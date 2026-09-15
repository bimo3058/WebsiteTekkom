import re

with open("script/data/temp.txt", "r", encoding="utf-16") as f:
    text = f.read()

# Let's find all occurences of x-teleport
for m in re.finditer(r'<template x-teleport="body">', text):
    print("Teleport found at", m.start())

