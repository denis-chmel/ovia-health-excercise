# OviaHealth Incentive Module

# Goal

Build a system that rewards the user for logging their health-related events. Rewards rules are set by the user's employer. Among all the factors performance and user experience are preferred the most.

## UX acceptance criteria
There are two UX approaches to solve this:
- sync: user receives a reward notification right with the confirmation of a logged event
- async: user receives a reward after some time, say via email or a mobile push notification

From the performance point of view, the "async" approach is feasible, but from a UX point, it's better to give feedback (reward) right after submitting the event.
Both ways are similar in design except the sync ways is slightly more challenging, however it rewards user right away which could be a good UX gesture.
So let's agree to strive to show all eligible rewards right upon the event submission, as long as this process takes less than 250 ms.

## Data Structure / Scope
We must detect all eligible rewards for the submitted event in the shortest amount of time.
So the amount of involved data matters, let's identify the worst-case scenario for the performance.

Assuming OviaHealth has centralized storage, keeps events of millions of users, each user has at most 1 current employer.
The most active user is going to enter 10 events per his average day. The most generous employer is going to have up to 20 incentive programs at the same time.
As for the max period of the history of events -- let's not worry about more than 2 years for now -- long enough to figure out how popular this module is, detect common patterns and situational bottlenecks to eliminate.

So when such user submits an event we potentially need to recall and process all his events for 2 years.
That is 2 * 365 * 10 per day ~= 7500 events in total, they all are potentially playing a role in getting a reward, so
all should be checked against the currently active incentives of each current employer, that is up to 20 incentives.

However:
- we can assume that the same event cannot be rewarded with the same incentive more than one time. So then when checking the certain incentive program there is no need to check events that were logged before the last rewarded event for the given incentive program.
- we may reduce the number of potential incentive programs (to check events against) to only eligible ones (by finding which event types the incentive program works with).
- we may reduce the number of events that the incentive must check to only eligible ones (by checking only N days of events for the "N days in a row" type).

## Storage Types
The choices are: relational DB, NoSQL one, or a mix of two.

When checking events of a user it is critical to load given user events in the shortest time possible,
therefore user's events might make sense to keep in key-value storage, one record per user. Earned rewards in turn
can be stored as an optional attribute of an event.

For example let's use MongoDB collection for keeping user's events, 1 record per user. Mongo is shardeable, so should
not be a problem for huge amount of user-generated events.
There is a 16MB limit on one record in Mongo, let's calculate how much data can we store per user:
- event type (normalzie in RDBS, keep id here, 4 bytes)
- event value (string, say 50 bytes max)
- timestamp (2 bytes)
- an array of reward_ids (~0-8 bytes)

That allows ~250000 of events in 1 record, ~33 years for a marginally active user, good enough.

As for the other models - they are not so sensitive storage-wise, so can be stored in any system,
traditional RDBS is fine enough, to have normalized relations.

## Database Structure

![DB Structure](https://github.com/denis-chmel/ovia-health-excercise/blob/main/docs/db-structure.png?raw=true)

The reward is just a json for now, should be formalized somehow better, but that's out of scope here.

# Implementation
Please see the code and [tests](https://github.com/denis-chmel/ovia-health-excercise/tree/main/app/Module/EventReward/Test), the only pattern I used is Strategy, it was naturally needed to get a polymorphism here,
satisfy OCP, to allow to extensively add new rule types without modifying existent classes.
ORM or ActiveRecord is required, but I have omitted this here (tests are using a non-persistent imitation of storage,
with the help of repository abstraction layer).

# Try me

```shell
composer install
npm test
```

# Next steps
- I've cut corners on detailed algorytm rules, all edge cases must be implemented and covered with unit-tests,
  (e.g. surely an event should not be rewarded by same incentive more than one time, trivial but I skipped that for now)
- ideally to cover rules logic with benchmark tests (on a huge DB fixture)
- try to mix sync and async approach: before checking against incentives - sort them by "simple to check first", reward all one by one until 250ms is reached, send the remaining (if any) to a background (queue)
- think about a call that "prewarms" user's past event while he is entering a new one (could be useful to also encourage the user to share his/her mood when we detected that user is one step away from a reward there)

### Suggest a couple ways we could share the user incentive achievements with incentive-managing partners outside of Ovia Health while balancing data security, performance and user experience. Feel free to make assumptions about our partners’ technical systems.

Not fully sure what you desire to hear on this topic, let me just think out loud:
- user's events is sensitive data, so I guess there must be a mechanism that allows each partner to request a permission from certain user to access his/her info, user must have an ability to revoke permit any time. In short - something similar to Facebook/Twitter/etc auth.
- when a partner consumer is subscribing to certain user events, then if nobody insisted on a "push" approach here I would stick to a simple "pull" - send the request (say via rest), get a paginated response back (if has access to)
- what could be another way? hard to say, definitely not an email as it's insecure. "Push", but to where? Nightly pre-generated CSV/Json/XML file - sure, but potential waste of resources if nobody will come for it.
- SSL is a must when transferring sensitive info
- to not reinvent the wheel I would try to stick to Oauth 2.0 / JWT specifications for authentication
