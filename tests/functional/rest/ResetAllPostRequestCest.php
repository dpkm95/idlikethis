<?php

namespace rest;

class ResetAllPostRequestCest {

	/**
	 * @test
	 * it should not reset comments if post id is missing from POST request
	 */
	public function it_should_not_reset_comments_if_post_id_is_missing_from_post_request(\FunctionalTester $I) {
		$post_id = $I->havePostInDatabase();
		$comment_ids = $I->haveManyCommentsInDatabase(3, $post_id, ['comment_type' => 'idlikethis']);

		$I->loginAsAdmin();
		$I->amEditingPostWithId($post_id);

		$wp_rest_nonce = $I->grabValueFrom('input[name="rest_nonce"]');
		$I->haveHttpHeader('X-WP-Nonce', $wp_rest_nonce);

		$I->sendAjaxPostRequest('/wp-json/idlikethis/v1/admin/reset-all', [
		]);

		$I->seeResponseCodeIs(400);
		foreach ($comment_ids as $comment_id) {
			$I->seeCommentInDatabase(['comment_ID' => $comment_id, 'comment_post_ID' => $post_id]);
		}
	}

	/**
	 * @test
	 * it should not reset comments if post id is not valid
	 */
	public function it_should_not_reset_comments_if_post_id_is_not_valid(\FunctionalTester $I) {
		$post_id = $I->havePostInDatabase();

		$I->loginAsAdmin();
		$I->amEditingPostWithId($post_id);

		$wp_rest_nonce = $I->grabValueFrom('input[name="rest_nonce"]');
		$I->haveHttpHeader('X-WP-Nonce', $wp_rest_nonce);

		$I->sendAjaxPostRequest('/wp-json/idlikethis/v1/admin/reset-all', [
			'post_id' => 3344,
		]);

		$I->seeResponseCodeIs(400);
	}

	/**
	 * @test
	 * it should not reset comments if user cannot edit posts
	 */
	public function it_should_not_reset_comments_if_user_cannot_edit_posts(\FunctionalTester $I) {
		$post_id = $I->havePostInDatabase();
		$comment_ids = $I->haveManyCommentsInDatabase(3, $post_id, ['comment_type' => 'idlikethis']);

		$I->loginAsAdmin();
		$I->amEditingPostWithId($post_id);

		$I->sendAjaxPostRequest('/wp-json/idlikethis/v1/admin/reset-all', [
			'post_id' => $post_id,
		]);

		$I->seeResponseCodeIs(403);
		foreach ($comment_ids as $comment_id) {
			$I->seeCommentInDatabase(['comment_ID' => $comment_id, 'comment_post_ID' => $post_id]);
		}
	}

	/**
	 * @test
	 * it should reset comments when post id is valid
	 */
	public function it_should_reset_comments_when_post_id_is_valid(\FunctionalTester $I) {
		$post_id = $I->havePostInDatabase();
		$comment_ids = $I->haveManyCommentsInDatabase(3, $post_id, ['comment_type' => 'idlikethis']);

		$I->loginAsAdmin();
		$I->amEditingPostWithId($post_id);

		$wp_rest_nonce = $I->grabValueFrom('input[name="rest_nonce"]');
		$I->haveHttpHeader('X-WP-Nonce', $wp_rest_nonce);

		$I->sendAjaxPostRequest('/wp-json/idlikethis/v1/admin/reset-all', [
			'post_id' => $post_id,
		]);

		$I->seeResponseCodeIs(200);
		foreach ($comment_ids as $comment_id) {
			$I->dontSeeCommentInDatabase(['comment_ID' => $comment_id, 'comment_post_ID' => $post_id]);
		}
	}
}