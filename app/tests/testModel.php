<?php
namespace Test;
require_once('../../public_html/index.php');

class ModelTest extends \PHPUnit_Framework_TestCase{

	public function generateEditora() {
		$editora           = new \Models\Editora();
		$editora->nome     = "Elsevier";
		$editora->endereco = "Rua antonia vieira de sá";
		$editora->estado   = "RN";
		$editora->cidade   = "Mossoro";
		$editora->telefone = "331231232";

		return $editora;
	}
	public function testEditora(){
		$editora = $this->generateEditora();
		$lastEdId = $editora->save();


		$editora2 = new \Models\Editora($lastEdId);

		$this->assertEquals($editora,$editora2);

		$editora2->nome = "Novo titulo";
		$editora2->save();

		$editora = new \Models\Editora($lastEdId);

		$this->assertEquals($editora,$editora2);

		$allEd = \Models\Editora::fetchAll();

		$this->assertGreaterThanOrEqual(1, count($allEd));

		$this->assertTrue($editora->delete());


	}

	public function testLivro(){
		$livro = new \Models\Livro();
		$livro->titulo = "Teste";
		$livro->subtitulo = "subtitulo";
		$livro->edvolume = "1 edicao";
		$livro->ISBN = "12312-12323";
		$livro->CDUCDD = "1222-223";
		$livro->classificacaolocal = '2';
		$livro->nExemplares = '5';

		$editora = $this->generateEditora();
		$livro->idEditoras = $editora->save();

		$lastId = $livro->save();

		$livro2 = new \Models\Livro($lastId);
		$this->assertEquals($livro, $livro2);

		$livro2->titulo = "teste2";
		$livro2->save();

		$livro = new \Models\Livro($lastId);

		$this->assertEquals($livro, $livro2);

		$this->assertTrue($livro2->delete());
		$this->assertTrue($editora->delete());
	}
}
