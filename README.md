## Installation

- git clone https://github.com/wizag-ke/WIZDMS.git
- cd WIZDMS
- composer install --ignore-platform-reqs
- npm install
- npm run dev
- cp .env.example .env
- php artisan key:generate
- add you dbnane to env
-add email credentials
- php artisan migrate --seed

## Default User
U:developer@developer.biz
P:developer


## Initial setup after instalation
- add organisation details
- add roles
- add atleast three users -approvers and a super approver
- add workflow
- add workflow limit
- add approvers
- add email Templates - atleast three
- assign email templates at email settings
- add a document
- add document details -ensure the doc_id matches document id you added above
- visit this route croncommand
- check your email inbox
# setUp
