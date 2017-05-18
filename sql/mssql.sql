CREATE TABLE /*_*/FlowThread (
	flowthread_id BINARY(11) NOT NULL PRIMARY KEY,
	flowthread_pageid BIGINT(10) NOT NULL,
	flowthread_userid BIGINT(10) NOT NULL,
	flowthread_username NVARCHAR(255) NOT NULL,
	flowthread_text TEXT NOT NULL,
	flowthread_parentid BINARY(11),
	flowthread_status SMALLINT(1) NOT NULL,
	flowthread_like INT(4) NOT NULL,
	/* These two fields are not marked as unsigned to avoid possible inconsistency in system to cause them wrap around */
	flowthread_report INT(4) NOT NULL
) /*$wgDBTableOptions*/;

CREATE INDEX /*i*/FlowThreadSearchByPage ON /*_*/FlowThread(flowthread_pageid, flowthread_parentid);

CREATE TABLE /*_*/FlowThreadAttitude (
	flowthread_att_id BINARY(11) NOT NULL,
	flowthread_att_type SMALLINT(1) NOT NULL,
	flowthread_att_userid BIGINT(10) NOT NULL
)/*$wgDBTableOptions*/;

CREATE INDEX /*i*/FlowThreadSearchAttitude ON /*_*/FlowThreadAttitude(flowthread_att_id, flowthread_att_userid);
