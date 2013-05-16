#light

module PemToCspBlob.Main

open CommandLine
open CommandLine.Text
open Org.BouncyCastle.Crypto
open Org.BouncyCastle.Crypto.Parameters
open Org.BouncyCastle.OpenSsl
open Org.BouncyCastle.Security
open System
open System.IO
open System.Security.Cryptography

type Options() = 
    
    [<Option("i", "in", Required = true, HelpText = "Input filename, must be a PEM formatted RSA key")>]
    member val InputFile : string = null with get, set
    [<Option("o", "out", Required = true, HelpText = "Output filename")>]
    member val OutputFile : string = null with get, set
    [<HelpOption>]
    member this.GetUsage() =
        HelpText.AutoBuild(this).ToString()

let doConversion inf outf =
    use r = new StreamReader(new FileStream(inf, FileMode.Open))
    let pr = new PemReader(r)
    let kp = pr.ReadObject() :?> AsymmetricCipherKeyPair
    let rsa = DotNetUtilities.ToRSAParameters(kp.Private :?> RsaPrivateCrtKeyParameters);
    let prov = new RSACryptoServiceProvider()
    prov.PersistKeyInCsp <- false
    prov.ImportParameters(rsa)
    use w = new FileStream(outf, FileMode.Create)
    let blob = prov.ExportCspBlob(true)
    w.Write(blob, 0, blob.Length)

[<EntryPoint>]
let main argv = 
    let parser = new CommandLineParser(new CommandLineParserSettings(Console.Error))
    let options = new Options()
    if parser.ParseArguments(argv, options) |> not then
        1
    else       
        try   
            doConversion options.InputFile options.OutputFile
            0
        with _ as e ->
            printfn "Conversion failed with the following error: %s" e.Message
            2
