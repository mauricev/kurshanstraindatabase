<?php

declare(strict_types=1);

require_once(__DIR__ . '/../classes/classes_search.php');

final class SearchTestFailure extends RuntimeException {}

function search_test_assert(bool $condition, string $message): void {
	if (!$condition) {
		throw new SearchTestFailure($message);
	}
}

function search_test_fetch_ids(string $sql, int $minimumCount, array $fallback): array {
	try {
		$db = new Peri_Database();
		$stmt = $db->sqlPrepare($sql);
		$stmt->execute();
		$ids = array_map('strval', $stmt->fetchAll(PDO::FETCH_COLUMN, 0));

		if (count($ids) >= $minimumCount) {
			return array_slice($ids, 0, $minimumCount);
		}
	} catch (Throwable $e) {
		return $fallback;
	}

	return array_slice(array_merge($ids, $fallback), 0, $minimumCount);
}

function search_test_fixture_ids(): array {
	return [
		'genes' => search_test_fetch_ids('SELECT gene_id FROM gene_table ORDER BY gene_id LIMIT 2', 2, ['101', '102']),
		'alleles' => search_test_fetch_ids('SELECT allele_id FROM allele_table ORDER BY allele_id LIMIT 2', 2, ['201', '202']),
		'balancers' => search_test_fetch_ids('SELECT balancer_id FROM balancer_table ORDER BY balancer_id LIMIT 2', 2, ['301', '302']),
		'transgenes' => search_test_fetch_ids('SELECT transgene_id FROM transgene_table ORDER BY transgene_id LIMIT 2', 2, ['401', '402']),
		'antibiotics' => search_test_fetch_ids('SELECT antibiotic_id FROM antibiotic_table ORDER BY antibiotic_id LIMIT 2', 2, ['501', '502']),
		'fluorotags' => search_test_fetch_ids('SELECT fluoroTag_id FROM fluoro_tag_table ORDER BY fluoroTag_id LIMIT 2', 2, ['601', '602']),
	];
}

function search_test_run_with_post(array $post, callable $callback): mixed {
	$oldPost = $_POST;
	$_POST = $post;
	ob_start();
	set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
		throw new ErrorException($message, 0, $severity, $file, $line);
	});
	try {
		$result = $callback();
		ob_end_clean();
		return $result;
	} catch (Throwable $e) {
		ob_end_clean();
		throw $e;
	} finally {
		restore_error_handler();
		$_POST = $oldPost;
	}
}

function search_test_assert_search_executes(string $name, array $post, callable $callback): void {
	$result = search_test_run_with_post($post, $callback);
	search_test_assert(is_array($result), "$name did not return a result array.");
}

function search_test_balancer_clause(array $balancerIds, bool $orSearch): array {
	return search_test_run_with_post(
		array_filter([
			'balancersArray_htmlName' => $balancerIds,
			'balancerName_chkbox_htmlName' => $orSearch ? 'on' : null,
		], static fn($value) => $value !== null),
		static function (): array {
			$join = new InnerJoinerForStrains();
			$params = [];
			$having = [];
			$search = new BalancersSearchForStrains($join, $params, $having);

			return [
				'where' => $search->concatElementWhereClauseToMasterWhereClause(''),
				'params' => $params,
				'having' => $having,
			];
		}
	);
}

function search_test_antibiotic_clause(array $antibioticIds, bool $orSearch): array {
	return search_test_run_with_post(
		array_filter([
			'antibioticArray_htmlName' => $antibioticIds,
			'antibiotic_chkbox_htmlName' => $orSearch ? 'on' : null,
		], static fn($value) => $value !== null),
		static function (): array {
			$join = new InnerJoinerForPlasmids();
			$params = [];
			$having = [];
			$search = new AntibioticsSearchForPlasmids($join, $params, $having);

			return [
				'where' => $search->concatElementWhereClauseToMasterWhereClause(''),
				'params' => $params,
				'having' => $having,
			];
		}
	);
}

function search_test_fluorotag_clause(array $fluorotagIds, bool $orSearch): array {
	return search_test_run_with_post(
		array_filter([
			'fluorotagArray_htmlName' => $fluorotagIds,
			'fluoroTag_chkbox_htmlName' => $orSearch ? 'on' : null,
		], static fn($value) => $value !== null),
		static function (): array {
			$join = new InnerJoinerForPlasmids();
			$params = [];
			$having = [];
			$search = new FluoroTagSearchForPlasmids($join, $params, $having);

			return [
				'where' => $search->concatElementWhereClauseToMasterWhereClause(''),
				'params' => $params,
				'having' => $having,
			];
		}
	);
}

function search_test_assert_or_clause(string $name, array $clause): void {
	search_test_assert(str_contains($clause['where'], ' OR '), "$name should contain OR between selected values.");
	search_test_assert(!str_contains($clause['where'], ' in ('), "$name should not use IN/HAVING syntax for an OR search.");
	search_test_assert(count($clause['having']) === 0, "$name should not create HAVING requirements for an OR search.");
}

function search_test_assert_and_clause(string $name, array $clause, int $selectedCount): void {
	search_test_assert(str_contains($clause['where'], ' in ('), "$name should use IN syntax when OR search is unchecked.");
	search_test_assert(!str_contains($clause['where'], ' OR '), "$name should not contain OR when OR search is unchecked.");
	search_test_assert(count($clause['having']) === 1, "$name should add one HAVING count requirement.");
	search_test_assert(reset($clause['having']) === $selectedCount, "$name should require all selected values in HAVING.");
}

function search_test_cases(array $ids): array {
	return [
		[
			'name' => 'comments OR gene plus allele path executes',
			'post' => [
				'comment_htmlName' => 'test',
				'genesArray_htmlName' => [$ids['genes'][0]],
				'allelesArray_htmlName' => [$ids['alleles'][0]],
				'geneName_chkbox_htmlName' => 'on',
				'alleleName_chkbox_htmlName' => 'on',
			],
			'callback' => static fn() => searchDatabaseForStrains(),
		],
		[
			'name' => 'comments AND gene path executes',
			'post' => [
				'comment_htmlName' => 'test',
				'commentsANDeverythingelse_chkbox_htmlName' => 'on',
				'genesArray_htmlName' => [$ids['genes'][0]],
				'geneName_chkbox_htmlName' => 'on',
			],
			'callback' => static fn() => searchDatabaseForStrains(),
		],
		[
			'name' => 'comments AND allele path executes',
			'post' => [
				'comment_htmlName' => 'test',
				'commentsANDeverythingelse_chkbox_htmlName' => 'on',
				'allelesArray_htmlName' => [$ids['alleles'][0]],
				'alleleName_chkbox_htmlName' => 'on',
			],
			'callback' => static fn() => searchDatabaseForStrains(),
		],
		[
			'name' => 'comments AND transgene path executes',
			'post' => [
				'comment_htmlName' => 'test',
				'commentsANDeverythingelse_chkbox_htmlName' => 'on',
				'transgeneArray_htmlName' => [$ids['transgenes'][0]],
				'transgeneName_chkbox_htmlName' => 'on',
			],
			'callback' => static fn() => searchDatabaseForStrains(),
		],
		[
			'name' => 'plasmid antibiotics path executes',
			'post' => [
				'plasmidArray_htmlName' => [''],
				'antibioticArray_htmlName' => $ids['antibiotics'],
				'antibiotic_chkbox_htmlName' => 'on',
			],
			'callback' => static fn() => searchDatabaseForPlasmids(),
		],
		[
			'name' => 'plasmid antibiotics AND path executes',
			'post' => [
				'plasmidArray_htmlName' => [''],
				'antibioticArray_htmlName' => $ids['antibiotics'],
			],
			'callback' => static fn() => searchDatabaseForPlasmids(),
		],
		[
			'name' => 'plasmid fluorotags path executes',
			'post' => [
				'plasmidArray_htmlName' => [''],
				'fluorotagArray_htmlName' => $ids['fluorotags'],
				'fluoroTag_chkbox_htmlName' => 'on',
			],
			'callback' => static fn() => searchDatabaseForPlasmids(),
		],
		[
			'name' => 'plasmid fluorotags AND path executes',
			'post' => [
				'plasmidArray_htmlName' => [''],
				'fluorotagArray_htmlName' => $ids['fluorotags'],
			],
			'callback' => static fn() => searchDatabaseForPlasmids(),
		],
	];
}

$ids = search_test_fixture_ids();
$failures = [];
$passes = 0;

foreach (search_test_cases($ids) as $case) {
	try {
		search_test_assert_search_executes($case['name'], $case['post'], $case['callback']);
		echo "PASS: {$case['name']}\n";
		$passes++;
	} catch (Throwable $e) {
		echo "FAIL: {$case['name']}: {$e->getMessage()}\n";
		$failures[] = $case['name'];
	}
}

$shapeCases = [
	['balancers OR clause shape', static fn() => search_test_assert_or_clause('balancers OR clause shape', search_test_balancer_clause($ids['balancers'], true))],
	['balancers AND clause shape', static fn() => search_test_assert_and_clause('balancers AND clause shape', search_test_balancer_clause($ids['balancers'], false), count($ids['balancers']))],
	['antibiotics OR clause shape', static fn() => search_test_assert_or_clause('antibiotics OR clause shape', search_test_antibiotic_clause($ids['antibiotics'], true))],
	['antibiotics AND clause shape', static fn() => search_test_assert_and_clause('antibiotics AND clause shape', search_test_antibiotic_clause($ids['antibiotics'], false), count($ids['antibiotics']))],
	['fluorotags OR clause shape', static fn() => search_test_assert_or_clause('fluorotags OR clause shape', search_test_fluorotag_clause($ids['fluorotags'], true))],
	['fluorotags AND clause shape', static fn() => search_test_assert_and_clause('fluorotags AND clause shape', search_test_fluorotag_clause($ids['fluorotags'], false), count($ids['fluorotags']))],
];

foreach ($shapeCases as [$name, $callback]) {
	try {
		$callback();
		echo "PASS: $name\n";
		$passes++;
	} catch (Throwable $e) {
		echo "FAIL: $name: {$e->getMessage()}\n";
		$failures[] = $name;
	}
}

echo "\n$passes passed, " . count($failures) . " failed.\n";

if (count($failures) > 0) {
	exit(1);
}
