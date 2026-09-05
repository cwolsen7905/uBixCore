# Feature Tests System

> **Status: Planning — open for discussion.** This document is a proposal, not an implementation spec. It captures the current Feature Tests system as of 2026-04-21 and sketches the intended direction (new `Feature_Tests_Users` table, split internal/external datetime fields, sunset `group_column` + UDF usage). Nothing here is committed to; comments, counter-proposals, and scope adjustments are welcome before any code changes begin.

---

We have developed the Feature Tests system to support multiple simultaneous projects. 
Currently it is being used to control the Mobile 2025 Interface A/B tests. 
And I would like to use it for upcoming AgeVerification control (as our implementations get established and we move forward, we can transition that control into the distinct AV logic and tools). And we will need this to support FE testing new responsive versions of mobile-forward email templates. 

A Feature Test is defined by one or more rows in the Feature_Test_Groups table which are linked by the "feature" identifier, so that each row specifies a portion or whole of the users which will get assigned a given "group_marker". The marker will be saved in the DB. Presently, the "group_column" stores values like "udf02" indicating the marker is stored in the RV tables. Given that we're only bucketing users at Registration time presently, the logic then uses "user_type" (set as reg or coreg) to determine if the group_column applies to the RV or the RV1 table. 

We are currently out of space on udf fields. In support of the new projects, it is time to expand the system by adding a Feature_Tests_Users table. This will store one row per user per Feature they are participating in. So a user may have one row for Mobile_2025 (marker indicating new interface), and a second row for AgeVer (marker indicating AgeGo). 

The logic will need to be adjusted so that if group_column indicates a udf0x field, we still use the appropriate RV table, but if it indicates a constant ("FTUsers"?) we will look up their row in the new table based on "user_id" and "feature". 

I didn't want to go so far as to store a table name in the DB and make it that dynamic, at least at this stage. We are safer making sure this is somewhat code-driven, even if that means cases in the code. Ultimately, once we're done with Mobile2025, we should stop using UDF altogether, and the group_column field could be dropped, unless we still would like to use this system to employ fields in other existing tables as the marker. I don't think we should lean in that direction. So you can write the code assuming we can drop the group_column along with support for UDF later. 

Later on, we might extract Groups from Features even further and put the marker values in the Groups table, but with how I see us wanting to control things, I think they're good in one table for now. In other words, I don't see enough high-level configuration of a Feature Test that's not specific to the groups. We have more control this way - leaving it dumb where "feature" is the only thing linking the groups together. 

I would also like to modify the existing table to enhance our control of how users receive test data. 

Let's go over what we've got, serving as the foundation of documentation, and then I'll itemize the task requests more specifically. 


Feature Tests system

The code presently uses these tables:

flirt4free.Feature_Test_Groups
     Defines a Feature Test and its groups.
`
CREATE TABLE `Feature_Test_Groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `feature` varchar(20) NOT NULL COMMENT 'feature label',
  `sitekey` varchar(20) NOT NULL DEFAULT '' COMMENT 'Empty for all, or sitekey flirt4free|whitelabel|xvc|xvt',
  `domain` varchar(255) NOT NULL DEFAULT '' COMMENT 'Empty for all, given if test restricted to being active on this domain (or multiple rows)',
  `user_type` varchar(10) NOT NULL DEFAULT '' COMMENT 'Empty for all, VIP|Premium|Basic|Guest if we want to limit it',
  `feature_state` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'The state or default of the feature for this group',
  `group_column` varchar(20) NOT NULL DEFAULT '' COMMENT 'the RV field to record group value in',
  `group_marker` varchar(20) NOT NULL COMMENT 'test group label - and marker for UDF fields',
  `split` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '0 to 100, integer percent of users to bucket in this group',
  `internal_only` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT 'Set to N when ready to be live for All users',
  `start_datetime` datetime DEFAULT NULL,
  `bucket_end_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `feature_sitekey_domain_group` (`feature`,`sitekey`,`domain`,`user_type`,`group_marker`),
  KEY `feature_group` (`feature`,`group_marker`),
  KEY `sitekey_domain` (`sitekey`,`domain`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_c
`

flirt4free.Feature_Test_Event_Log
     A generic log table storing foreign keys and data that is meaningful in the context of the test. This is where BI will do their work evaluating the results, and bridging outward to other tables.  The type of events are specific to the test being run - the feature itself. 
`
CREATE TABLE `Feature_Test_Event_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `feature_test_group_id` smallint(4) unsigned NOT NULL DEFAULT 0 COMMENT 'external key for flirt4free.Feature_Test_Groups',
  `event_type` varchar(10) NOT NULL DEFAULT '' COMMENT 'specific to test and logged event',
  `user_id` int(10) unsigned DEFAULT NULL COMMENT 'null if guest user',
  `guest_id` varchar(20) DEFAULT NULL COMMENT 'guest user identification marker',
  `feature_state` varchar(3) NOT NULL DEFAULT '' COMMENT 'version or active flag',
  `event_value` mediumint(7) NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `feature_state` (`feature_test_group_id`,`feature_state`),
  KEY `feature_event_state` (`feature_test_group_id`,`event_type`,`feature_state`)
) ENGINE=InnoDB AUTO_INCREMENT=845423 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci
`

The code is centralized here: 

php-live-components/FeatureTests.php
We'll keep this to helper functions and core FeatureTests logic. 
Things specific to the feature being handled should be located with that control code. 
For example, with Mobile 2025, we are setting tpl vars that FE and JS use in the chat room. So on controller page load, we assess the user's test group and apply tpl vars that are meaningful for the feature's implementation. 


The code is used by:

LiveSite → loginUsingWebservice()
     Store session data on manual logins and non-container cookie remember-me

Controller → root/index.php    
     Store session data on cookie remember-me

Controller www-my-account → root/login.php
     Store session data on manual logins on Sandbox and Dev environments (only at this time)

Reg WS → Registration.php 
      Bucket users into tests upon registration. 

Controller → User.php→updateMobileFeatureState()
     Used to control Mobile 2025 A/B behavior based on FeatureTests configurations. 

You can see that there's only 3 points of contact: 
where/when the user gets bucketed into a test group.  This could ultimately be Reg, or on Login, or on page load (controller+livesite) where it can scoop guest users. 
build session data. For guest users or page load situation, this would be set up upon bucketing. For current uses, session data is built on login. So this code appears in 3 places to cover our login processes. As we support guest users, Login should replace with the user's assigned group, or assign the guest-selected bucket if missing (if appropriate for the test). 
Control Feature. The code controlling the feature will want to determine if the user is part of the test at all, and possibly control a feature's behavior based on the user's assigned group. This could mean PHP code operates in a certain way, or TPL vars are passed on. 
The control code for a Feature Test could be in many places - such as send-register-user webservice, or code specific to a JS modal like the 18+ popup (as so controlled by PHP via tpl vars). It merely needs to detect a group, and behave as desired. 


TASK REQUESTS

Expand support for internal vs external control in the Feature_Test_Groups table.

To review, we need 3 timestamps per test. 
The first is when to begin making the test available. 
The second is when to stop adding people to the test. 
The third is when to stop making the test available to users already bucketed. 
The idea being, either we're stopping a test because it didn't succeed, so we're leaving the existing functionality in place and all test users should revert to that behavior (session data activating B functionality no longer being set). Or, we've made the B functionality the new normal/default, so we can stop paying attention to user bucketing and timestamps. 

Currently we're controlling who gets the tests and who gets bucketed into the tests with 4 fields: 
internal_only: Y = only internal users get bucketed or session data. N = everyone can get access to the test. 
start_datetime: when bucketing and session data may begin, filtered by internal flag by code. 
bucket_end_datetime: when users (new to a test) stop being assigned a marker. 
end_datetime: when the feature itself stop becoming available (because session data is no longer provided - leaving all the traces of who was part of the test behind for analysis and record). 

In practice, the intent was to set everything up, be able to test internally in production, and flip the internal_only flag to N when a test should go live. BI isn't really using the dates for filtering queries. In practice they begin with the Events table or collect users from where the marker is stored (like RV, or the new table). But it is nice to have a record of events, so the idea was we could update the start_datetime after, if we felt like it (to match external launch time). 

But when internal_only is N, internal and external users can get bucketed etc. So how do we layer and test in production the next phase of a multi-stage test on a feature (case in point - various stages of mob 25 testing groups)? To date this works, but only because the rows are being selected in order. So if test 4 buckets me using udf02 and test 5 overwrites it internally, I'm good to test, but this relies on assumptions about how the code works. 

So I think the best solution is to ditch internal_only and change it to be: 
internal_start_datetime
internal_bucket_end_datetime
internal_end_datetime
external_start_datetime
external_bucket_end_datetime
external_end_datetime
So that we can control internal versus external access completely independently and they can remain true start times without modifying them after "launch", etc. 
Yes, we may think of this as Test-specific, but based on how complicated it can be to code the front end etc, it find it's easier to allow the Test_Groups configuration layer onto that in such a way that either the FE can code to group_ids (or other tpl vars), or we can assign markers to FE that specify how the functionality behaves, distinctly from group identifiers, so we can run variations of tests controlling one or more aspects. An example of this would be Mob 25, where VIP users are given one test group via code on the session data (but no udf marker in DB) and how multiple stages of tests and "rollouts" where all new regs get the B result for now for verification purposes, but still log. (end the A/B, start a 100% → B configuration) 
The dates are useful in making RV queries faster, but we're not going to rely on that for much longer. 
So tempting as it is to extract Tests from Groups, I think the table is still best as-is for now, and just expanding the internal/external time support will suffice. But if you have other ideas I'm open to discussion. 

2. Modify code to support new datetime fields. 

This would involve setting up values for currently-running tests in the DB. 
And then modifying the code to use the new fields and no longer rely on internal_only. 
And finally dropping the unused column. 


3. Add Feature_Tests_Users table

Build a new table Feature_Tests_Users 
The table would be inserted to when a user is bucketed into a test.
The row would refer to the Feature_Test_Groups row the user was assigned. 
The row would refer to the user by id or fingerprint (etc). 
The table would be unique on user_id and test_group_id so that a user can only receive one marker per Feature Test. 
The marker in the row replaces the need for RV.udf fields and avoids collisions and limited space and enables easier querying. 
I believe it will be helpful to record what event triggered a user to be bucketed into a test, and a data pointer to the event where possible. For example, event=reg, event_id=RV.id. event=guest_visit, event_id=lander_x (or homepage)

Here's my initial concept of the fields for this table: 
id, rv_id, rv1_id, user_id, guest_id, test_group_id, bucket_event, bucket_event_id, datetime

I am wondering if it might be better to do:
user_id_type = RV | opti | fingerprint | email
user_id_value = ^ (not calling it user_id to avoid some confusion with an 'always optiuser_id' field)

I think the only major event_id that would overlap with this fields would be reg-related, but I don't see a problem with it. It can be left blank in that case if we wanted. 

rv/1 and user_id - we fill out what we have at the time of bucketing.
test_group_id - key of flirt4free.Feature_Test_Groups telling us which specific test.
test_group_bucket - A, B, C etc marker (originally I thought we'd want the marker string - which is theoretically human-readable and could be used as a tpl var value, here as well, but it doesn't seem necessary to me now)
bucket_event  - reg, addcc, unsubscribe, just tracking when/where we bucketed them
bucket_event_id - for when it's not reg, might be nice to store a foreign key here, which table aligns with bucket_event.

4. Modify code to use Feature_Tests_Users table 

Virtually all of the code will need to be touched to support this change, but I think only the code inside the class. Because the session and bucketing are just calling functions. After we stop supporting udf fields we don't even need bucketing to return anything, especially if that code can immediately begin updating the php session. 

And because code using the buckets, like controller User.php, is just accessing the session data, it doesn't care how the bucket is stored or generated deeper than that. 

Testing
I'll attach my code. It's some very basic unit tests that I run in SB command line in:
/code/_TEST_SCRIPTS/
so it's not committed. 
This may be helpful in verifying your changes. 

The goal with this task is to get the new functionality up in time to configure some 100% tests of new AgeVerification stuff. It will be guest user based, we may need to just store bucket selection in session if we don't have a fingerprint (otherwise it'd be an orphan row in the Users table) and scoop that into reg WS later for the insert when bucketing happens then. (meaning, we'd update the bucketing function to see if session already had a group selection in it)

We will start a new task for the AV usage, where we can make those changes and implement the code which takes the user's bucket and instructions JS via tpl vars. For now I'd like to convert the existing system over to this new code and data.