<?php
namespace idlikethis\MetaBoxes;

use idlikethis_MetaBoxes_VotesDisplayMetaBox as VotesDisplayMetaBox;
use Prophecy\Argument;

class VotesDisplayMetaBoxTest extends \Codeception\TestCase\WPTestCase
{

    /**
     * @var \idlikethis_Repositories_CommentsRepositoryInterface
     */
    protected $commments_repository;

    /**
     * @var \idlikethis_Texts_VotesMetaBoxTextProviderInterface
     */
    protected $texts_provider;

    /**
     * @var \idlikethis_Templates_RenderEngineInterface
     */
    protected $render_engine;

    public function setUp()
    {
        // before
        parent::setUp();

        // your set up methods here
        $this->commments_repository = $this->prophesize("idlikethis_Repositories_CommentsRepositoryInterface");
        $this->render_engine = $this->prophesize('idlikethis_Templates_RenderEngineInterface');
        $this->texts_provider = $this->prophesize("idlikethis_Texts_VotesMetaBoxTextProviderInterface");
    }

    public function tearDown()
    {
        // your tear down methods here

        // then
        parent::tearDown();
    }

    /**
     * @test
     * it should be instantiatable
     */
    public function it_should_be_instantiatable()
    {
        $sut = $this->make_instance();

        $this->assertInstanceOf('idlikethis_MetaBoxes_VotesDisplayMetaBox', $sut);
    }

    /**
     * @test
     * it should render an empty message if there are no idlikethis comments associated to the post
     */
    public function it_should_render_an_empty_message_if_there_are_no_idlikethis_comments_associated_to_the_post()
    {
        $post = $this->factory()->post->create_and_get();

        $this->commments_repository->get_comments_for_post($post)->willReturn([]);
        $this->texts_provider->get_empty_comments_text()->shouldBeCalled();
        $this->render_engine->render(Argument::any(), Argument::any())->shouldBeCalled();

        $sut = $this->make_instance();
        $sut->render($post, []);
    }

    /**
     * @test
     * it should render the comments count display if there are idlikethis comments associated with the post
     */
    public function it_should_render_the_comments_count_display_if_there_are_idlikethis_comments_associated_with_the_post()
    {
        $post = $this->factory()->post->create_and_get();
        $comments = $this->factory()->comment->create_many(10);

        $this->commments_repository->get_comments_for_post($post)->willReturn(['first idea' => array_splice($comments, 0, 5), 'second idea' => $comments]);
        $this->texts_provider->get_comments_title_text()->shouldBeCalled();
        $this->render_engine->render(Argument::any(), Argument::any())->shouldBeCalled();

        $sut = $this->make_instance();
        $sut->render($post, []);
    }

    private function make_instance()
    {
        return new VotesDisplayMetaBox($this->commments_repository->reveal(), $this->render_engine->reveal(), $this->texts_provider->reveal());
    }

}