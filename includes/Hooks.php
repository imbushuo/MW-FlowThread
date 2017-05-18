<?php
namespace FlowThread;

class Hooks {

	public static function getFilteredNamespace() {
		$ret = array(
			NS_MEDIAWIKI,
			NS_TEMPLATE,
			NS_CATEGORY,
			NS_FILE,
		);
		if (defined('NS_MODULE')) {
			$ret[] = NS_MODULE;
		}

		return $ret;
	}

	private static function allowComment(\Title $title) {
		// Disallow commenting on pages without article id
		if ($title->getArticleID() == 0) {
			return false;
		}

		if ($title->isSpecialPage()) {
			return false;
		}

		// These could be explicitly allowed in later version
		if (!$title->canTalk()) {
			return false;
		}

		if ($title->isTalkPage()) {
			return false;
		}

		// No commenting on main page
		if ($title->isMainPage()) {
			return false;
		}

		// Blacklist several namespace
		if (in_array($title->getNamespace(), self::getFilteredNamespace())) {
			return false;
		}

		return true;
	}

	public static function onBeforePageDisplay(\OutputPage &$output, \Skin &$skin) {
		if (!static::allowComment($output->getTitle())) {
			return true;
		}

		// Do not display when printing
		if ($output->isPrintable()) {
			return true;
		}

		// Disable if not viewing
		if ($skin->getRequest()->getVal('action', 'view') != 'view') {
			return true;
		}

		if ($output->getUser()->isAllowed('commentadmin-restricted')) {
			$output->addJsConfigVars(array('commentadmin' => ''));
		}

		global $wgFlowThreadConfig;
		$config = array(
			'Avatar' => $wgFlowThreadConfig['Avatar'],
			'AnonymousAvatar' => $wgFlowThreadConfig['AnonymousAvatar'],
		);

		if (\FlowThread\Post::canPost($output->getUser())) {
			$output->addJsConfigVars(array('canpost' => ''));
		} else {
			$config['CantPostNotice'] = wfMessage('flowthread-ui-cantpost')->toString();
		}

		global $wgFlowThreadConfig;
		$output->addJsConfigVars(array('wgFlowThreadConfig' => $config));
		$output->addModules('ext.flowthread');
		return true;
	}

	public static function onBeforePageDisplayMobile(\OutputPage &$output, \Skin &$skin) {
		if (!static::allowComment($output->getTitle())) {
			return true;
		}

		// Do not display when printing
		if ($output->isPrintable()) {
			return true;
		}

		// Disable if not viewing
		if ($skin->getRequest()->getVal('action', 'view') != 'view') {
			return true;
		}
		
		if ($output->getUser()->isAllowed('commentadmin-restricted')) {
			$output->addJsConfigVars(array('commentadmin' => ''));
		}

		global $wgFlowThreadConfig;
		$config = array(
			'Avatar' => $wgFlowThreadConfig['Avatar'],
			'AnonymousAvatar' => $wgFlowThreadConfig['AnonymousAvatar'],
		);

		if (\FlowThread\Post::canPost($output->getUser())) {
			$output->addJsConfigVars(array('canpost' => ''));
		} else {
			$config['CantPostNotice'] = wfMessage('flowthread-ui-cantpost')->toString();
		}

		global $wgFlowThreadConfig;
		$output->addJsConfigVars(array('wgFlowThreadConfig' => $config));
		$output->addModules('ext.flowthread.mobile');
		return true;
	}

	public static function onLoadExtensionSchemaUpdates($updater) {
		$dir = __DIR__ . '/../sql';

		$dbType = $updater->getDB()->getType();
		// For non-MySQL/MariaDB/SQLite DBMSes, use the appropriately named file
		switch ($dbType)
		{
			case 'mysql':
				$filename = 'mysql.sql';
				break;
			case 'sqlite':
				$filename = 'mysql.sql';
				break;
			case 'mssql':
				$filename = 'mssql.sql';
				break;
			default:
				throw new \Exception('Database type not currently supported');
		}

		$updater->addExtensionTable('FlowThread', "{$dir}/{$filename}");
		$updater->addExtensionTable('FlowThreadAttitude', "{$dir}/{$filename}");

		return true;
	}

	public static function onArticleDeleteComplete(&$article, \User &$user, $reason, $id, \Content $content = null, \LogEntry $logEntry) {
		$page = new Page($id);
		$page->limit = -1;
		$page->fetch();
		$page->erase();
		return true;
	}

	public static function onBaseTemplateToolbox(\BaseTemplate &$baseTemplate, array &$toolbox) {
		if (isset($baseTemplate->data['nav_urls']['usercomments'])
			&& $baseTemplate->data['nav_urls']['usercomments']) {
			$toolbox['usercomments'] = $baseTemplate->data['nav_urls']['usercomments'];
			$toolbox['usercomments']['id'] = 't-usercomments';
		}
	}

	public static function onSkinTemplateOutputPageBeforeExec(&$skinTemplate, &$tpl) {
		$user = $skinTemplate->getRelevantUser();

		if ($user && $skinTemplate->getUser()->isAllowed('commentadmin-restricted')) {
			$nav_urls = $tpl->get('nav_urls');
			$nav_urls['usercomments'] = [
				'text' => wfMessage('sidebar-usercomments')->text(),
				'href' => \SpecialPage::getTitleFor('FlowThreadManage')->getLocalURL(array(
					'user' => $user->getName(),
				)),
			];
			$tpl->set('nav_urls', $nav_urls);
		}

		return true;
	}
}
