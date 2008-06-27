
INSERT INTO `configurations` VALUES 
(1,'max_absent_time','90','Maximum Days An Account Can Be Inactive');

INSERT INTO `evaluations` (`id`, `name`, `precedence_id`, `function_id`, `group`) VALUES 
(1, 'EV_SSO', 0, 25, 'EVIDENCE'),
(2, 'EV_FSA', 1, 26, 'EVIDENCE'),
(3, 'EV_IVV', 2, 27, 'EVIDENCE'),
(4, 'EST', 3, 0, 'ACTION'),
(5, 'SSO', 4, 0, 'ACTION');

