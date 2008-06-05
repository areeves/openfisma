INSERT INTO `users` VALUES 
(17,'root','202cb962ac59075b964b07152d234b70','Application Administrator','Admin',NULL,'User','0000-00-00 00:00:00','0000-00-00 00:00:00',':9d1fee901b933a42978f2eacbcddff65:7b24afc8bc80e548d66c4e7ff72171c5','0000-00-00 00:00:00','0000-00-00 00:00:00',1,0,'555-555-5555','555-555-5555','admin@openfisma.org','root_r');

INSERT INTO `configurations` VALUES 
(1,'max_absent_time','90','Absent days of an account causing being disabled');

INSERT INTO `evaluations` (`id`, `name`, `precedence_id`, `function_id`, `group`) VALUES 
(1, 'EV_SSO', 0, 0, 1),
(2, 'EV_FSA', 1, 0, 1),
(3, 'EV_IVV', 2, 0, 1),
(4, 'EST', 0, 0, 2),
(5, 'SSO', 4, 0, 2);

