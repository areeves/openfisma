
INSERT INTO `configurations` VALUES 
(1,'max_absent_time','90','Absent days of an account causing being disabled');

INSERT INTO `evaluations` (`id`, `name`, `precedence_id`, `function_id`, `group`) VALUES 
(1, 'EV_SSO', 0, 0, 'EVIDENCE'),
(2, 'EV_FSA', 1, 0, 'EVIDENCE'),
(3, 'EV_IVV', 2, 0, 'EVIDENCE'),
(4, 'EST', 0, 0, 'ACTION'),
(5, 'SSO', 4, 0, 'ACTION');

/* default user roles setup in openfisma */
INSERT INTO `roles` VALUES 
(3,'Information System Security Officer','ISSO','[NIST Special Publication 800-37 Definition] The information system security officer is the individual responsible to the authorizing official, information system owner, or the senior agency information security officer for ensuring the appropriate operational security posture is maintained for an information system or program. The information system security officer also serves as the principal advisor to the authorizing official, information system owner, or senior agency information security officer on all matters (technical and otherwise) involving the security of the information system. The information system security officer typically has the detailed knowledge and expertise required to manage the security aspects of the information system and, in many agencies, is assigned responsibility for the day-to-day security operations of the system. This responsibility may also include, but is not limited to, physical security, personnel security, incident handling, and security training and awareness. The information system security officer may be called upon to assist in the development of the system security policy and to ensure compliance with that policy on a routine basis. In close coordination with the information system owner, the information system security officer often plays an active role in developing and updating the system security plan as well as in managing and controlling changes to the system and assessing the security impact of those changes. [OpenFISMA definition] The Information Systems Security Officer Group is designed to allow Information System Security Officers the ability to identify, assess, prioritize, and monitor the progress of corrective efforts for security weaknesses found in thier system.'),
(5,'Reviewer','REVIEWER','[OpenFISMA Definition] The Reviewer Group gives users the ability to view all information on the Plan of Actions and Milestones report for the information system. They do not have the ability to create, edit, or delete any information.'),
(6,'System Operational Staff','SOP','This group represents the userbase who will be fixing the identified security weaknesses. They have rights to create mitigation strategies, upload evidence, and view and generate reports on open and closed findings. An example would be a System Admin responsible for patching the Windows Servers, or Database Admin in charge of a particular database.'),
(7,'Independent Verification and Validation','IV&V','The Independent Verification and Validation group serves as a third party independent of the correction process who validates whether or not the security weakness has been successfully closed. They have the ability to view all agency Plan of Actions and Milestone information and the ability to close an open item.'),
(8,'Senior Agency Information Security Officer','SAISO','[NIST 800-37 Definition] The senior agency information security officer is the agency official responsible for: (i) carrying out the Chief Information Officer responsibilities under FISMA; (ii) possessing professional qualifications, including training and experience, required to administer the information security program functions; (iii) having information security duties as that official’s primary duty; and (iv) heading an office with the mission and resources to assist in ensuring agency compliance with FISMA. The senior agency information security officer (or supporting staff member) may also serve as the authorizing officials designated representative. The senior agency information security officer serves as the Chief Information Officer’s primary liaison to the agency’s authorizing officials, information system owners, and information system security officers. [OpenFISMA Definition] The SAISO has the ability to view all agency wide Plan of Actions and Milestone information as well as generate reports and provide comments and guidance to the Information System Security Officers through the remediation page.'),
(10,'Application Administrator','ADMIN','[OpenFISMA Definition] The Application Administrators group provides administrative privileges to the OpenFISMA application. Users will have the ability to access all security controls and functions.'),
(11,'Certification Agent','AUDITOR','[NIST 800-37 Definition] The certification agent is an individual, group, or organization responsible for conducting a security certification, or comprehensive assessment of the management, operational, and technical security controls in an information system to determine the extent to which the controls are implemented correctly, operating as intended, and producing the desired outcome with respect to meeting the security requirements for the system. The certification agent also provides recommended corrective actions to reduce or eliminate vulnerabilities in the information system. Prior to initiating the security assessment activities that are a part of the certification process, the certification agent provides an independent assessment of the system security plan to ensure the plan provides a set of security controls for the information system that is adequate to meet all applicable security requirements. [OpenFISMA Definition] This group is used by independent auditors to view finding information, create findings, set initial risk levels, and provide recommended corrective actions.'),
(13,'Information System Owner','ISO','[NIST 800-37 Definition] The information system owner is an agency official responsible for the overall procurement, development, integration, modification, or operation and maintenance of an information system. The information system owner is responsible for the development and maintenance of the system security plan and ensures the system is deployed and operated according to the agreed-upon security requirements. The information system owner is also responsible for deciding who has access to the information system (and with what types of privileges or access rights) and ensures that system users and support personnel receive the requisite security training (e.g., instruction in rules of behavior). The information system owner informs key agency officials of the need to conduct a security certification and accreditation of the information system, ensures that appropriate resources are available for the effort, and provides the necessary system-related documentation to the certification agent. The information system owner receives the security assessment results from the certification agent. After taking appropriate steps to reduce or eliminate vulnerabilities, the information system owner assembles the security accreditation package and submits the package to the authorizing official or the authorizing official’s designated representative for adjudication. The role of information system owner can be interpreted in a variety of ways depending on the particular agency and the system development life cycle phase of the information system. Some agencies may refer to information system owners as program managers or business/asset/mission owners. In some situations, the notification of the need to conduct a security certification and accreditation may come from the senior agency information security officer or authorizing official as they endeavor to ensure compliance with federal or agency policy. The responsibility for ensuring appropriate resources are allocated to the security certification and accreditation effort depends on whether the agency uses a centralized or decentralized funding mechanism. Depending on how the agency has organized and structured its security certification and accreditation activities, the authorizing official may choose to designate an individual other than the information system owner to compile and assemble the information for the accreditation package. In this situation, the designated individual must coordinate the compilation and assembly activities with the information system owner.'),
(14, 'AO', 'AO', 'The authorizing official (or designated approving/accrediting authority as referred to by some agencies) is a senior management official or executive with the authority to formally assume responsibility for operating an information system at an acceptable level of risk to agency operations, agency assets, or individuals. The authorizing official should have the authority to oversee the budget and business operations of the information system within the agency and is often called upon to approve system security requirements, system security plans, and memorandums of agreement and/or memorandums of understanding. The AO issues a formal approval to operate and information system, an interim authorization to operate the information system under specific terms and conditions; or deny authorization to operate the information system (or if the system is already operational, halt operations) if unacceptable security risks exist.');

/* default user functions setup in openfimsa */
INSERT INTO `functions` (`function_id`, `function_name`, `function_screen`, `function_action`, `function_desc`, `function_open`) VALUES 
(1, 'read dashboard', 'dashboard', 'read', '', '1'),
(2, 'read finding', 'finding', 'read', '', '1'),
(3, 'update finding', 'finding', 'update', '', '1'),
(4, 'create finding', 'finding', 'create', '', '1'),
(5, 'delete finding', 'finding', 'delete', '', '1'),
(6, 'read asset', 'asset', 'read', '', '1'),
(7, 'update asset', 'asset', 'update', '', '1'),
(8, 'create asset', 'asset', 'create', '', '1'),
(9, 'delete asset', 'asset', 'delete', '', '1'),
(10, 'read remediation', 'remediation', 'read', '', '1'),
(11, 'create injection', 'remediation', 'create_injection', '', '1'),
(12, 'update finding', 'remediation', 'update_finding', '', '1'),
(13, 'delete remediation', 'remediation', 'delete', '', '1'),
(14, 'update course of action', 'remediation', 'update_course_of_action', '', '1'),
(15, 'update finding assignment', 'remediation', 'update_finding_assignment', '', '1'),
(16, 'update control assignment', 'remediation', 'update_control_assignment', '', '1'),
(17, 'update countermeasures', 'remediation', 'update_countermeasures', '', '1'),
(18, 'update threat', 'remediation', 'update_threat', '', '1'),
(19, 'update finding recommendation', 'remediation', 'update_finding_recommendation', '', '1'),
(20, 'update finding resources', 'remediation', 'update_finding_resources', '', '1'),
(21, 'update est completion date', 'remediation', 'update_est_completion_date', '', '1'),
(22, 'read evidence', 'remediation', 'read_evidence', '', '1'),
(23, 'update evidence', 'remediation', 'update_evidence', '', '1'),
(24, 'update mitigation strategy approval', 'remediation', 'update_mitigation_strategy_approval', '', '1'),
(25, 'update evidence approval first', 'remediation', 'update_evidence_approval_first', '', '1'),
(26, 'update evidence approval second', 'remediation', 'update_evidence_approval_second', '', '1'),
(27, 'update evidence approval third', 'remediation', 'update_evidence_approval_third', '', '1'),
(28, 'update risk first', 'remediation', 'update_risk_first', '', '1'),
(29, 'update risk second', 'remediation', 'update_risk_second', '', '1'),
(30, 'update risk third', 'remediation', 'update_risk_third', '', '1'),
(31, 'read report', 'report', 'read', '', '1'),
(32, 'generate poam report', 'report', 'generate_poam_report', '', '1'),
(33, 'generate fisma report', 'report', 'generate_fisma_report', '', '1'),
(34, 'generate general report', 'report', 'generate_general_report', '', '1'),
(35, 'read vulnerability', 'vulnerability', 'read', '', '1'),
(36, 'update vulnerability', 'vulnerability', 'update', '', '1'),
(37, 'create vulnerability', 'vulnerability', 'create', '', '1'),
(38, 'delete vulnerability', 'vulnerability', 'delete', '', '1'),
(39, 'generate system rafs', 'report', 'generate_system_rafs', '', '1'),
(40, 'overdue report', 'report', 'generate_overdue_report', '', '1'),
(41, 'update finding course of action', 'remediation', 'update_finding_course_of_action', '', '1'),
(42, 'read Users', 'admin_users', 'read', '', '1'),
(43, 'update Users', 'admin_users', 'update', '', '1'),
(44, 'Delete Users', 'admin_users', 'delete', '', '1'),
(45, 'create Users', 'admin_users', 'create', '', '1'),
(58, 'read System Groups', 'admin_system_groups', 'read', '', '1'),
(59, 'Delete System Groups', 'admin_system_groups', 'delete', '', '1'),
(60, 'update System Groups', 'admin_system_groups', 'update', '', '1'),
(61, 'Create System Groups', 'admin_system_groups', 'create', '', '1'),
(66, 'read Systems', 'admin_systems', 'read', '', '1'),
(67, 'Delete Systems', 'admin_systems', 'delete', '', '1'),
(68, 'update Systems', 'admin_systems', 'update', '', '1'),
(69, 'Create Systems', 'admin_systems', 'create', '', '1');

/* default user role functions in openfisma*/
INSERT INTO `role_functions` (`role_func_id`, `role_id`, `function_id`) VALUES 
(1, 5, 1),(2, 5, 2),(3, 5, 6),(4, 5, 10),(5, 5, 22),(6, 5, 31),(7, 5, 32),(8, 5, 35),(9, 5, 40),(10, 3, 1),(11, 3, 2),(12, 3, 6),(13, 3, 10),(14, 3, 22),(15, 3, 31),(16, 3, 32),(17, 3, 35),(18, 3, 7),(19, 3, 8),(20, 3, 3),(21, 3, 4),(22, 3, 11),(23, 3, 12),(24, 3, 16),(25, 3, 17),(26, 3, 18),(27, 3, 39),(28, 3, 36),(29, 3, 37),(30, 3, 14),(31, 3, 41),(32, 3, 20),(33, 3, 21),(34, 3, 24),(35, 3, 23),(36, 3, 25),(37, 3, 28),(38, 7, 1),(39, 7, 6),(40, 7, 10),(41, 7, 22),(42, 7, 31),(43, 7, 32),(44, 7, 35),(45, 7, 26),(46, 7, 29),(47, 7, 39),(48, 5, 40),(49, 14, 1),(50, 14, 6),(51, 14, 10),(52, 14, 22),(53, 14, 31),(54, 14, 32),(55, 14, 35),(56, 14, 40),(57, 14, 34),(58, 14, 39),(59, 13, 1),(60, 13, 6),(61, 13, 10),(62, 13, 22),(63, 13, 31),(64, 13, 32),(65, 13, 35),(66, 13, 40),(67, 13, 34),(68, 13, 39),(69, 13, 34),(70, 13, 20),(71, 13, 21),(72, 13, 30),(73, 8, 1),(74, 8, 2),(75, 8, 6),(76, 8, 10),(77, 8, 22),(78, 8, 27),(79, 8, 31),(80, 8, 32),(81, 8, 35),(82, 8, 33),(83, 8, 34),(84, 8, 39),(85, 8, 40),(86, 11, 1),(87, 11, 2),(88, 11, 6),(89, 11, 10),(90, 11, 15),(91, 11, 19),(92, 11, 22),(93, 11, 31),(94, 11, 32),(95, 11, 35),(96, 11, 7),(97, 11, 8),(98, 11, 3),(99, 11, 4),(100, 11, 11),(101, 11, 12),(102, 11, 16),(103, 11, 17),(104, 11, 18),(105, 11, 39),(106, 11, 36),(107, 11, 37),(111, 3, 40),(139, 10, 42),(140, 10, 43),(141, 10, 44),(142, 10, 45),(155, 10, 58),(156, 10, 59),(157, 10, 60),(162, 10, 61),(163, 10, 66),(164, 10, 67),(165, 10, 68),(166, 10, 69),(171, 10, 2),(172, 10, 5),(173, 10, 10),(174, 10, 13),(175, 10, 10),(176, 10, 13),(177, 10, 1),(178, 10, 6),(179, 10, 1),(180, 10, 6),(181, 10, 1),(182, 10, 6),(183, 10, 1),(184, 10, 6),(185, 10, 10),(186, 10, 13),(187, 10, 1),(188, 10, 6),(189, 10, 7),(190, 10, 8),(191, 10, 9),(192, 10, 31),(193, 10, 32),(194, 10, 33),(195, 10, 34),(196, 10, 39),(197, 10, 40),(198, 10, 35),(199, 10, 36),(200, 10, 37),(201, 10, 38),(213, 13, 14),(214, 3, 34),(215, 15, 5),(216, 16, 5),(217, 16, 4),(224, 17, 5);
