CREATE TABLE `slideshows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(10) CHARACTER SET latin1 NOT NULL DEFAULT 'nl',
  `extra_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `dataset_id` int(11) DEFAULT NULL,
  `module` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `hide_button_navigation` enum('N','Y') DEFAULT 'N',
  `hide_paging` enum('N','Y') DEFAULT 'N',
  `speed` int(5) DEFAULT 7000,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;


CREATE TABLE `slideshows_datasets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(255) NOT NULL,
  `method` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE `slideshows_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slideshow_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `data` text DEFAULT NULL,
  `sequence` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE `slideshows_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET latin1 NOT NULL,
  `settings` text CHARACTER SET latin1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;


INSERT INTO slideshows_types(type, settings) VALUES('basic', NULL);
