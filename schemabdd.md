Schema Quiz API
========================

Game
- id
- name
- nbPlayerMin
- nbPlayerMax
- description

Party
- code
- game#

Player
- id
- name
- party#
- owner
- score

Question
- id
- question

Answer
- id
- question#
- answer
- goodAnswer

Tag
- id
- name