
INSERT INTO `configurations` VALUES 
(1,'max_absent_time','90','Absent days of an account causing being disabled');

INSERT INTO `evaluations` (`id`, `name`, `precedence_id`, `function_id`, `group`) VALUES 
(1, 'EV_SSO', 0, 0, 'EVIDENCE'),
(2, 'EV_FSA', 1, 0, 'EVIDENCE'),
(3, 'EV_IVV', 2, 0, 'EVIDENCE'),
(4, 'EST', 3, 0, 'ACTION'),
(5, 'SSO', 4, 0, 'ACTION');

