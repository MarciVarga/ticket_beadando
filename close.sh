#!/bin/bash

mysql -u root yii2advanced << EOF
  update ticket set is_open=0
  where is_open=1
  and (select id from user
    where is_admin=1
      and id=(select user_id from comment
      where user_id=(select max(create_time) from comment
        where ticket_id=ticket.id)))
  and (select create_time from comment
    where user_id=ticket.user_id)<now()-interval 14 day);
EOF